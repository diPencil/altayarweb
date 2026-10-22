<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exceptions\IdempotencyConflictException;
use App\Http\Controllers\Admin\ManageMilesController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPointTransaction;
use App\Models\User;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "========================================================\n";
echo "   AltayarVIP Miles Subsystem — Phase 3 Admin Audit     \n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition(bool $condition, string $message): void
{
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] {$message}\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$message}\n";
        $failCount++;
    }
}

DB::beginTransaction();

try {
    // 0. Setup disposable test fixtures
    $testUser = new User();
    $testUser->firstname = 'Phase3';
    $testUser->lastname = 'Tester';
    $testUser->username = 'p3test_' . Str::random(6);
    $testUser->email = $testUser->username . '@altayarvip.test';
    $testUser->password = bcrypt('secret');
    $testUser->status = 1;
    $testUser->balance = 500.00; // Wallet balance
    $testUser->dashboard_permissions = null; // Unrestricted legacy
    $testUser->save();

    MembershipPointTransaction::create([
        'user_id' => $testUser->id,
        'trx' => getTrx(),
        'type' => 'earned',
        'points' => 120,
        'balance_after' => 120,
        'remark' => 'Initial loyalty points fixture',
    ]);

    MembershipCashbackTransaction::create([
        'user_id' => $testUser->id,
        'trx' => getTrx(),
        'type' => 'earned',
        'amount' => 50.00,
        'balance_after' => 50.00,
        'remark' => 'Initial cashback fixture',
    ]);

    $rootAdmin = new Admin();
    $rootAdmin->name = 'Super Admin';
    $rootAdmin->username = 'superadmin_' . Str::random(4);
    $rootAdmin->email = $rootAdmin->username . '@test.com';
    $rootAdmin->password = bcrypt('secret');
    $rootAdmin->save();

    $unprivilegedEmp = new Employee();
    $unprivilegedEmp->username = 'emp_none_' . Str::random(4);
    $unprivilegedEmp->page_permissions = [];
    $unprivilegedEmp->user_permissions = [];

    $viewOnlyEmp = new Employee();
    $viewOnlyEmp->username = 'emp_view_' . Str::random(4);
    $viewOnlyEmp->page_permissions = ['miles'];
    $viewOnlyEmp->user_permissions = ['miles_view'];

    $grantOnlyEmp = new Employee();
    $grantOnlyEmp->username = 'emp_grant_' . Str::random(4);
    $grantOnlyEmp->page_permissions = ['miles'];
    $grantOnlyEmp->user_permissions = ['miles_view', 'miles_grant'];

    $milesService = app(MilesService::class);
    $controller = new ManageMilesController($milesService);
    $usersController = new ManageUsersController();

    // ----------------------------------------------------
    // Scenario 1: Admin grants 1000 miles
    // ----------------------------------------------------
    echo "Scenario 1: Admin grants 1000 miles\n";
    $grantKey = 'grant_key_' . Str::random(10);
    $grantTrx = $milesService->grantMiles(
        user: $testUser,
        amount: 1000,
        source: 'admin',
        remark: 'Initial 1000 grant by admin',
        referenceId: 'REF-GRANT-01',
        adminId: $rootAdmin->id,
        idempotencyKey: $grantKey
    );
    assertCondition($grantTrx->type === 'credit', "Transaction type is 'credit'");
    assertCondition($grantTrx->miles === 1000, "Miles amount is exactly 1000");
    assertCondition($grantTrx->balance_after === 1000, "balance_after snapshot is 1000");
    assertCondition($grantTrx->admin_id === $rootAdmin->id, "Actor recorded correctly as admin ID {$rootAdmin->id}");

    // ----------------------------------------------------
    // Scenario 2: Admin deducts 200 miles
    // ----------------------------------------------------
    echo "\nScenario 2: Admin deducts 200 miles\n";
    $deductKey = 'deduct_key_' . Str::random(10);
    $deductTrx = $milesService->deductMiles(
        user: $testUser,
        amount: 200,
        source: 'admin',
        remark: 'Deduction of 200 miles by admin',
        referenceId: 'REF-DEDUCT-01',
        adminId: $rootAdmin->id,
        idempotencyKey: $deductKey
    );
    assertCondition($deductTrx->type === 'debit', "Transaction type is 'debit'");
    assertCondition($deductTrx->miles === 200, "Miles amount is exactly 200");
    assertCondition($deductTrx->direction === -1, "Direction is signed -1");

    // ----------------------------------------------------
    // Scenario 3: Available balance becomes 800
    // ----------------------------------------------------
    echo "\nScenario 3: Available balance becomes 800\n";
    $availableBalance = $milesService->calculateAvailableBalance($testUser->id);
    assertCondition($availableBalance === 800, "Authoritative available balance is exactly 800 (1000 - 200)");
    assertCondition($deductTrx->balance_after === 800, "Transaction snapshot balance_after is 800");
    assertCondition($testUser->miles_balance === 800, "User model dynamic miles_balance accessor is 800");

    // ----------------------------------------------------
    // Scenario 4: Duplicate submission does not create duplicate transactions
    // ----------------------------------------------------
    echo "\nScenario 4: Duplicate submission idempotency\n";
    $duplicateGrant = $milesService->grantMiles(
        user: $testUser,
        amount: 1000,
        source: 'admin',
        remark: 'Initial 1000 grant by admin',
        referenceId: 'REF-GRANT-01',
        adminId: $rootAdmin->id,
        idempotencyKey: $grantKey
    );
    assertCondition($duplicateGrant->id === $grantTrx->id, "Duplicate submission returned existing transaction ID {$grantTrx->id}");
    assertCondition($duplicateGrant->trx === $grantTrx->trx, "Trx codes match identically ({$grantTrx->trx})");
    $totalTrxForUser = UserMilesTransaction::where('user_id', $testUser->id)->count();
    assertCondition($totalTrxForUser === 2, "Ledger contains exactly 2 rows (no duplicate created)");

    // ----------------------------------------------------
    // Scenario 5: Reversal behaves correctly
    // ----------------------------------------------------
    echo "\nScenario 5: Reversal behaves correctly\n";
    $reversal = $milesService->reverseTransaction(
        transactionId: $deductTrx->id,
        reason: 'Mistaken deduction reversal',
        adminId: $rootAdmin->id
    );
    assertCondition($reversal->is_reversal === true, "Reversal entry marked is_reversal = true");
    assertCondition($reversal->type === 'credit', "Debit reversal creates compensating credit");
    assertCondition($reversal->direction === 1, "Compensating credit direction is +1");
    assertCondition($milesService->calculateAvailableBalance($testUser->id) === 1000, "Balance restored back to 1000");

    $deductTrx->refresh();
    assertCondition($deductTrx->is_reversed === true, "Original transaction marked is_reversed = true");
    assertCondition(!is_null($deductTrx->reversed_at), "Original transaction has reversed_at timestamp");

    // Double reversal protection
    $caughtDouble = false;
    try {
        $milesService->reverseTransaction($deductTrx->id, 'Double reverse attempt', $rootAdmin->id);
    } catch (\InvalidArgumentException $e) {
        $caughtDouble = true;
    }
    assertCondition($caughtDouble, "Double reversal rejected with InvalidArgumentException");

    // ----------------------------------------------------
    // Scenario 6: Insufficient balance is rejected
    // ----------------------------------------------------
    echo "\nScenario 6: Insufficient balance is rejected\n";
    $caughtInsufficient = false;
    try {
        $milesService->deductMiles(
            user: $testUser,
            amount: 5000, // Balance is only 1000
            source: 'admin',
            remark: 'Overdraft attempt',
            adminId: $rootAdmin->id
        );
    } catch (\InvalidArgumentException $e) {
        $caughtInsufficient = true;
    }
    assertCondition($caughtInsufficient, "Overdraft deduction rejected with InvalidArgumentException");
    assertCondition($milesService->calculateAvailableBalance($testUser->id) === 1000, "Balance remains safely at 1000");

    // ----------------------------------------------------
    // Scenario 7: Unauthorized employee cannot grant/deduct/reverse
    // ----------------------------------------------------
    echo "\nScenario 7: Unauthorized employee cannot grant/deduct/reverse\n";
    assertCondition(adminCanMiles('view', $unprivilegedEmp) === false, "Unauthorized employee CANNOT view miles");
    assertCondition(adminCanMiles('grant', $unprivilegedEmp) === false, "Unauthorized employee CANNOT grant miles");
    assertCondition(adminCanMiles('deduct', $unprivilegedEmp) === false, "Unauthorized employee CANNOT deduct miles");
    assertCondition(adminCanMiles('reverse', $unprivilegedEmp) === false, "Unauthorized employee CANNOT reverse miles");

    // ----------------------------------------------------
    // Scenario 8: View-only employee cannot mutate balances
    // ----------------------------------------------------
    echo "\nScenario 8: View-only employee cannot mutate balances\n";
    assertCondition(adminCanMiles('view', $viewOnlyEmp) === true, "View-only employee CAN view miles");
    assertCondition(adminCanMiles('grant', $viewOnlyEmp) === false, "View-only employee CANNOT grant miles");
    assertCondition(adminCanMiles('deduct', $viewOnlyEmp) === false, "View-only employee CANNOT deduct miles");
    assertCondition(adminCanMiles('reverse', $viewOnlyEmp) === false, "View-only employee CANNOT reverse miles");

    // ----------------------------------------------------
    // Scenario 9: Authorized employee can perform only permitted actions
    // ----------------------------------------------------
    echo "\nScenario 9: Authorized employee can perform only permitted actions\n";
    assertCondition(adminCanMiles('view', $grantOnlyEmp) === true, "Grant-only employee CAN view miles");
    assertCondition(adminCanMiles('grant', $grantOnlyEmp) === true, "Grant-only employee CAN grant miles");
    assertCondition(adminCanMiles('deduct', $grantOnlyEmp) === false, "Grant-only employee CANNOT deduct miles");
    assertCondition(adminCanMiles('reverse', $grantOnlyEmp) === false, "Grant-only employee CANNOT reverse miles");

    // ----------------------------------------------------
    // Scenario 10: Search, filters and pagination work
    // ----------------------------------------------------
    echo "\nScenario 10: Search, filters and pagination work\n";
    $searchByTrx = UserMilesTransaction::where('trx', $grantTrx->trx)->first();
    assertCondition(!is_null($searchByTrx) && $searchByTrx->id === $grantTrx->id, "Search by TRX finds exact transaction");

    $searchByUser = UserMilesTransaction::whereHas('user', fn($q) => $q->where('username', $testUser->username))->count();
    assertCondition($searchByUser >= 3, "Search by username matches all user transactions");

    $filterByCredit = UserMilesTransaction::where('user_id', $testUser->id)->where('type', 'credit')->count();
    assertCondition($filterByCredit === 2, "Filter by operation type 'credit' returns 2 records");

    $filterBySource = UserMilesTransaction::where('user_id', $testUser->id)->where('source', 'admin')->count();
    assertCondition($filterBySource === 2, "Filter by source 'admin' returns 2 records");

    $paginateTest = UserMilesTransaction::where('user_id', $testUser->id)->paginate(2);
    assertCondition($paginateTest->perPage() === 2, "Server-side pagination perPage is 2");
    assertCondition($paginateTest->total() === 3, "Server-side pagination total is 3");

    // ----------------------------------------------------
    // Scenario 11: Admin User Details displays correct Miles balance
    // ----------------------------------------------------
    echo "\nScenario 11: Admin User Details displays correct Miles balance\n";
    $userFresh = User::findOrFail($testUser->id);
    assertCondition($userFresh->miles_balance === 1000, "User fresh instance reflects 1000 miles");
    assertCondition($userFresh->milesTransactions()->count() === 3, "user->milesTransactions relation returns 3 entries");

    // ----------------------------------------------------
    // Scenario 12: Existing Wallet, Loyalty Points and Cashback remain unchanged
    // ----------------------------------------------------
    echo "\nScenario 12: Existing Wallet, Loyalty Points and Cashback remain unchanged\n";
    $userFresh->refresh();
    assertCondition((float) $userFresh->balance === 500.00, "Wallet balance remained exactly 500.00");
    assertCondition((int) $userFresh->membership_points_balance === 120, "Loyalty points remained exactly 120");
    assertCondition((float) $userFresh->cashback_balance === 50.00, "Cashback balance remained exactly 50.00");

} catch (\Throwable $e) {
    echo "\n[ERROR] Exception encountered during test execution: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $failCount++;
} finally {
    DB::rollBack();
    echo "\n[CLEANUP] Database rolled back cleanly. No testing records persisted in database.\n";
}

echo "\n========================================================\n";
echo "   Verification Results: {$passCount} Passed | {$failCount} Failed\n";
echo "========================================================\n\n";

exit($failCount === 0 ? 0 : 1);
