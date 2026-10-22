<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "=================================================================\n";
echo "   Employee Permission Persistence & Real Auth Guards Audit      \n";
echo "=================================================================\n\n";

DB::beginTransaction();

try {
    // 1. Employee existence and credential check
    $employee = Employee::find(1);
    if (!$employee) {
        throw new Exception("Employee #1 not found in local database.");
    }
    echo "Found Employee #1: {$employee->username} (email: {$employee->email})\n";

    // Reset password to a known hash for login verification
    $tempPassword = 'TestPassword123!';
    $employee->password = Hash::make($tempPassword);
    $employee->status = 1;
    $employee->save();

    // 2. Test Real Authentication Guard: Employee Login
    echo "\nStep 1: Testing auth('employee') guard authentication...\n";
    $authAttempt = Auth::guard('employee')->attempt([
        'username' => $employee->username,
        'password' => $tempPassword,
    ]);
    assertTest("Employee authentication succeeds with correct credentials", $authAttempt === true);
    assertTest("auth('employee')->check() returns true", Auth::guard('employee')->check() === true);
    assertTest("auth('employee')->id() matches employee ID 1", Auth::guard('employee')->id() === 1);
    assertTest("employee() helper returns current authenticated employee", employee() && employee()->id === 1);

    // Ensure Admin guard is NOT logged in (strict guard separation)
    assertTest("auth('admin')->check() is FALSE (no guard leaking)", Auth::guard('admin')->check() === false);

    // 3. Test Authentication Guard: Unauthenticated Access Blocked
    echo "\nStep 2: Testing unauthenticated route protection (RedirectIfNotEmployee)...\n";
    Auth::guard('employee')->logout();
    assertTest("After logout, auth('employee')->check() is false", Auth::guard('employee')->check() === false);
    assertTest("employee() helper returns null", employee() === null);

    $middleware = new \App\Http\Middleware\RedirectIfNotEmployee();
    $request = \Illuminate\Http\Request::create('/employee/dashboard', 'GET');
    $response = $middleware->handle($request, function () {
        return new \Illuminate\Http\Response('OK', 200);
    });
    assertTest("Unauthenticated request redirected to employee.login", $response->isRedirect(route('employee.login')));

    // 4. Test Employee Permission Persistence via Controller/DB
    echo "\nStep 3: Testing Employee permission persistence in database...\n";
    
    // Save state A: Grant miles_view and miles_grant
    $employee->user_permissions = ['miles_view', 'miles_grant'];
    $employee->save();
    $freshEmployee = Employee::find(1);
    assertTest("Saved user_permissions persisted as array in DB", is_array($freshEmployee->user_permissions));
    assertTest("user_permissions contains 'miles_view'", in_array('miles_view', $freshEmployee->user_permissions, true));
    assertTest("user_permissions contains 'miles_grant'", in_array('miles_grant', $freshEmployee->user_permissions, true));
    assertTest("user_permissions DOES NOT contain 'miles_deduct'", !in_array('miles_deduct', $freshEmployee->user_permissions, true));
    assertTest("user_permissions DOES NOT contain 'miles_reverse'", !in_array('miles_reverse', $freshEmployee->user_permissions, true));

    // Verify adminCanMiles reflects these exact permissions
    assertTest("adminCanMiles('view', employee) is TRUE", adminCanMiles('view', $freshEmployee) === true);
    assertTest("adminCanMiles('grant', employee) is TRUE", adminCanMiles('grant', $freshEmployee) === true);
    assertTest("adminCanMiles('deduct', employee) is FALSE (strictly protected)", adminCanMiles('deduct', $freshEmployee) === false);
    assertTest("adminCanMiles('reverse', employee) is FALSE (strictly protected)", adminCanMiles('reverse', $freshEmployee) === false);

    // Save state B: Unprivileged Employee (all miles permissions removed)
    $employee->user_permissions = ['users', 'bookings'];
    $employee->save();
    $freshEmployeeB = Employee::find(1);
    assertTest("State B: miles_view is absent", !in_array('miles_view', $freshEmployeeB->user_permissions, true));
    assertTest("adminCanMiles('view', employeeB) is FALSE (fail closed)", adminCanMiles('view', $freshEmployeeB) === false);
    assertTest("adminCanMiles('grant', employeeB) is FALSE", adminCanMiles('grant', $freshEmployeeB) === false);
    assertTest("adminCanMiles('deduct', employeeB) is FALSE", adminCanMiles('deduct', $freshEmployeeB) === false);
    assertTest("adminCanMiles('reverse', employeeB) is FALSE", adminCanMiles('reverse', $freshEmployeeB) === false);

    // Save state C: Empty array []
    $employee->user_permissions = [];
    $employee->save();
    $freshEmployeeC = Employee::find(1);
    assertTest("State C (empty array): adminCanMiles('view') is FALSE", adminCanMiles('view', $freshEmployeeC) === false);
    assertTest("State C (empty array): adminCanMiles('grant') is FALSE", adminCanMiles('grant', $freshEmployeeC) === false);
    assertTest("State C (empty array): adminCanMiles('deduct') is FALSE", adminCanMiles('deduct', $freshEmployeeC) === false);
    assertTest("State C (empty array): adminCanMiles('reverse') is FALSE", adminCanMiles('reverse', $freshEmployeeC) === false);

    // 5. Test Employee Route Guard (EmployeePagePermission)
    echo "\nStep 4: Testing EmployeePagePermission middleware guard...\n";
    $freshEmployeeC->user_permissions = ['bookings']; // Restricted: only bookings, NOT users
    $freshEmployeeC->save();
    Auth::guard('employee')->login($freshEmployeeC);

    $pageMiddleware = new \App\Http\Middleware\EmployeePagePermission();
    
    // Attempting to access users route when 'users' permission is NOT in user_permissions
    $usersRequest = \Illuminate\Http\Request::create('/employee/users', 'GET');
    $usersRequest->setRouteResolver(function () {
        $route = new \Illuminate\Routing\Route('GET', 'employee/users', ['as' => 'employee.users']);
        return $route;
    });

    $permResponse = $pageMiddleware->handle($usersRequest, function () {
        return new \Illuminate\Http\Response('OK', 200);
    });

    assertTest("Unprivileged access to employee.users redirected with 302", $permResponse->isRedirect());
    assertTest("Redirects to employee.home", $permResponse->isRedirect(route('employee.home')));

    // Now grant 'users' permission and verify access is allowed (200 OK)
    $freshEmployeeC->user_permissions = ['users'];
    $freshEmployeeC->save();
    Auth::guard('employee')->setUser($freshEmployeeC);

    $allowedResponse = $pageMiddleware->handle($usersRequest, function () {
        return new \Illuminate\Http\Response('OK', 200);
    });
    assertTest("Privileged employee is allowed through middleware (200 OK)", $allowedResponse->getStatusCode() === 200);

    echo "\n[CLEANUP] Rolling back database cleanly...\n";
    DB::rollBack();
    echo "[DONE] All Employee guards and permission persistence tests PASSED!\n";

} catch (Throwable $e) {
    DB::rollBack();
    echo "\n[ERROR] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

function assertTest(string $description, bool $condition): void {
    if ($condition) {
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
        throw new RuntimeException("Assertion failed: {$description}");
    }
}
