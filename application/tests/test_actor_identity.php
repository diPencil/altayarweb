<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "========================================================\n";
echo "   Actor Identity Model Verification (Admin vs Employee)\n";
echo "========================================================\n\n";

DB::beginTransaction();

try {
    $service = app(MilesService::class);

    // 1. Check existing root Admin #1 and Employee #1
    $admin1 = Admin::find(1);
    $employee1 = Employee::find(1);

    echo "Found Admin #1: " . ($admin1 ? $admin1->username : 'NOT FOUND') . "\n";
    echo "Found Employee #1: " . ($employee1 ? $employee1->username : 'NOT FOUND') . "\n";

    if (!$admin1 || !$employee1) {
        throw new Exception("Prerequisite Admin #1 or Employee #1 not found in local database.");
    }

    // 2. Create disposable test user
    $user = new User();
    $user->firstname = 'Actor';
    $user->lastname = 'TestUser';
    $user->username = 'actortest_' . Str::random(5);
    $user->email = 'actortest_' . Str::random(5) . '@test.com';
    $user->password = bcrypt('Password123!');
    $user->status = 1;
    $user->ev = 1;
    $user->sv = 1;
    $user->save();

    echo "Created disposable test user #{$user->id} ({$user->username})\n\n";

    // Test 1: Employee performs Grant
    echo "Test 1: Employee #1 performs Grant...\n";
    $trxEmployeeGrant = $service->grantMiles(
        user: $user,
        amount: 1500,
        remark: 'Employee grant test',
        source: 'admin',
        agentId: $employee1->id
    );

    assertTest("agent_id is {$employee1->id}", $trxEmployeeGrant->agent_id === $employee1->id);
    assertTest("admin_id is null", $trxEmployeeGrant->admin_id === null);
    assertTest("actor_name is '{$employee1->username} (Employee)'", $trxEmployeeGrant->actor_name === "{$employee1->username} (Employee)");
    assertTest("employee relationship resolves correctly", $trxEmployeeGrant->employee && $trxEmployeeGrant->employee->id === $employee1->id);
    assertTest("admin relationship is null (no collision with Admin #1)", $trxEmployeeGrant->admin === null);

    // Test 2: Admin performs Deduct
    echo "\nTest 2: Admin #1 performs Deduct...\n";
    $trxAdminDeduct = $service->deductMiles(
        user: $user,
        amount: 500,
        remark: 'Admin deduct test',
        source: 'admin',
        adminId: $admin1->id
    );

    assertTest("admin_id is {$admin1->id}", $trxAdminDeduct->admin_id === $admin1->id);
    assertTest("agent_id is null", $trxAdminDeduct->agent_id === null);
    assertTest("actor_name is '{$admin1->username} (Admin)'", $trxAdminDeduct->actor_name === "{$admin1->username} (Admin)");
    assertTest("admin relationship resolves correctly", $trxAdminDeduct->admin && $trxAdminDeduct->admin->id === $admin1->id);
    assertTest("employee relationship is null", $trxAdminDeduct->employee === null);

    // Test 3: System performs Grant (automated/system event)
    echo "\nTest 3: System performs Grant (null actors)...\n";
    $trxSystemGrant = $service->grantMiles(
        user: $user,
        amount: 200,
        remark: 'System automated grant',
        source: 'system'
    );

    assertTest("admin_id is null", $trxSystemGrant->admin_id === null);
    assertTest("agent_id is null", $trxSystemGrant->agent_id === null);
    assertTest("actor_name is 'System'", $trxSystemGrant->actor_name === 'System');

    // Test 4: Employee reverses Admin's transaction
    echo "\nTest 4: Employee #1 reverses Admin #1's deduct transaction...\n";
    $reversalByEmployee = $service->reverseTransaction(
        transactionId: $trxAdminDeduct->id,
        reason: 'Reversed by employee',
        agentId: $employee1->id
    );

    assertTest("Reversal agent_id is {$employee1->id}", $reversalByEmployee->agent_id === $employee1->id);
    assertTest("Reversal admin_id is null", $reversalByEmployee->admin_id === null);
    assertTest("Reversal actor_name is '{$employee1->username} (Employee)'", $reversalByEmployee->actor_name === "{$employee1->username} (Employee)");
    assertTest("Original transaction admin_id is preserved as {$admin1->id}", $trxAdminDeduct->fresh()->admin_id === $admin1->id);

    // Test 5: Verify foreign key integrity & deletion behavior
    echo "\nTest 5: Verify DB row directly via raw PDO...\n";
    $rawRow = DB::table('user_miles_transactions')->where('id', $trxEmployeeGrant->id)->first();
    assertTest("DB raw agent_id is exactly 1", (int)$rawRow->agent_id === 1);
    assertTest("DB raw admin_id is null", $rawRow->admin_id === null);

    echo "\n[CLEANUP] Rolling back transaction cleanly...\n";
    DB::rollBack();
    echo "[DONE] All actor identity tests passed with 0 persistence!\n";

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
