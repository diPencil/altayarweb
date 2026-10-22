<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exceptions\IdempotencyConflictException;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Illuminate\Support\Facades\DB;

$passed = 0;
$failed = 0;

function assertCondition(bool $condition, string $message)
{
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $message\n";
        $passed++;
    } else {
        echo "  [FAIL] $message\n";
        $failed++;
    }
}

echo "========================================================\n";
echo "   AltayarVIP Miles Subsystem — Phase 2 Verification    \n";
echo "========================================================\n\n";

DB::beginTransaction();

try {
    $service = app(MilesService::class);

    // Create disposable test user
    $user = new User();
    $user->firstname = 'Miles';
    $user->lastname = 'Tester';
    $user->username = 'testmiles_' . uniqid();
    $user->email = 'testmiles_' . uniqid() . '@example.com';
    $user->mobile = '+2010' . rand(10000000, 99999999);
    $user->password = bcrypt('password');
    $user->balance = 250.00;
    $user->status = 1;
    $user->ev = 1;
    $user->sv = 1;
    $user->save();

    $admin = Admin::first();
    if (!$admin) {
        $admin = new Admin();
        $admin->name = 'Test Admin';
        $admin->email = 'admin_test_' . uniqid() . '@example.com';
        $admin->username = 'admin_' . uniqid();
        $admin->password = bcrypt('password');
        $admin->save();
    }

    echo "Scenario 1: Grant Miles (Credit direction = +1)\n";
    $tx1 = $service->grantMiles(
        user: $user,
        amount: 1000,
        source: 'flight',
        remark: 'Promotional bonus flight',
        referenceId: 'FL-2026',
        adminId: $admin->id
    );
    assertCondition($tx1->type === 'credit', "Transaction type is 'credit'");
    assertCondition($tx1->direction === 1, "Transaction direction is +1");
    assertCondition($tx1->miles === 1000, "Miles amount is 1000");
    assertCondition($tx1->balance_after === 1000, "Balance after snapshot is 1000");
    assertCondition($service->calculateAvailableBalance($user->id) === 1000, "Authoritative ledger balance is 1000");
    assertCondition($user->miles_balance === 1000, "User miles_balance dynamic attribute is 1000");

    echo "\nScenario 2: Deduct Miles (Debit direction = -1)\n";
    $tx2 = $service->deductMiles(
        user: $user,
        amount: 200,
        source: 'manual',
        remark: 'Adjustment deduction',
        adminId: $admin->id
    );
    assertCondition($tx2->type === 'debit', "Transaction type is 'debit'");
    assertCondition($tx2->direction === -1, "Transaction direction is -1");
    assertCondition($tx2->miles === 200, "Miles amount is 200");
    assertCondition($tx2->balance_after === 800, "Balance after snapshot is 800");
    assertCondition($service->calculateAvailableBalance($user->id) === 800, "Authoritative balance updated to 800");
    $metrics = $service->getUserMetrics($user->id);
    assertCondition($metrics['available_miles'] === 800, "Metrics: available miles is 800");
    assertCondition($metrics['total_earned'] === 1000, "Metrics: total earned is 1000");
    assertCondition($metrics['total_deducted'] === 200, "Metrics: total deducted is 200");

    echo "\nScenario 3: Reject Deductions Exceeding Available Balance\n";
    $threwInsufficient = false;
    try {
        $service->deductMiles(
            user: $user,
            amount: 801,
            source: 'manual',
            remark: 'Excessive deduction'
        );
    } catch (\InvalidArgumentException $e) {
        $threwInsufficient = true;
    }
    assertCondition($threwInsufficient, "Throws InvalidArgumentException on insufficient balance");
    assertCondition($service->calculateAvailableBalance($user->id) === 800, "Balance remains safely at 800");

    echo "\nScenario 4: Reverse a Credit Transaction (Clawback: direction = -1)\n";
    $grantToReverse = $service->grantMiles(
        user: $user,
        amount: 500,
        source: 'hotel',
        remark: 'Erroneous bonus'
    );
    assertCondition($service->calculateAvailableBalance($user->id) === 1300, "Balance before credit reversal is 1300");

    $reversalOfCredit = $service->reverseTransaction(
        transactionId: $grantToReverse->id,
        reason: 'Customer cancelled booking',
        adminId: $admin->id
    );
    assertCondition($reversalOfCredit->type === 'debit', "Credit reversal type is 'debit'");
    assertCondition($reversalOfCredit->direction === -1, "Credit reversal direction is -1");
    assertCondition($reversalOfCredit->is_reversal === true, "is_reversal is true");
    assertCondition($reversalOfCredit->balance_after === 800, "Balance decreased by 500 to 800");
    $grantToReverse->refresh();
    assertCondition($grantToReverse->is_reversed === true, "Original transaction marked is_reversed = true");
    assertCondition(!is_null($grantToReverse->reversed_at), "Original transaction has reversed_at timestamp");

    echo "\nScenario 5: Reverse a Debit Transaction (Restoration: direction = +1)\n";
    $reversalOfDebit = $service->reverseTransaction(
        transactionId: $tx2->id,
        reason: 'Incorrect deduction reversed by admin',
        adminId: $admin->id
    );
    assertCondition($reversalOfDebit->type === 'credit', "Debit reversal type is 'credit'");
    assertCondition($reversalOfDebit->direction === 1, "Debit reversal direction is +1");
    assertCondition($reversalOfDebit->is_reversal === true, "is_reversal is true");
    assertCondition($reversalOfDebit->balance_after === 1000, "Balance restored back to 1000");

    echo "\nScenario 6: Double Reversal Protection\n";
    $threwDoubleReversal = false;
    try {
        $service->reverseTransaction(
            transactionId: $tx2->id,
            reason: 'Attempting second reversal'
        );
    } catch (\InvalidArgumentException $e) {
        $threwDoubleReversal = true;
    }
    assertCondition($threwDoubleReversal, "Rejects reversing an already-reversed transaction");

    echo "\nScenario 7: Reversal of Reversal Protection\n";
    $threwReversalOfReversal = false;
    try {
        $service->reverseTransaction(
            transactionId: $reversalOfDebit->id,
            reason: 'Attempting to reverse a reversal entry'
        );
    } catch (\InvalidArgumentException $e) {
        $threwReversalOfReversal = true;
    }
    assertCondition($threwReversalOfReversal, "Rejects reversing a reversal entry");

    echo "\nScenario 8: Strict Idempotency Handling\n";
    $idempKey = 'IDEMP_KEY_' . uniqid();
    $idempTx1 = $service->grantMiles(
        user: $user,
        amount: 350,
        source: 'promotional',
        remark: 'First submission',
        idempotencyKey: $idempKey
    );
    $idempTx2 = $service->grantMiles(
        user: $user,
        amount: 350,
        source: 'promotional',
        remark: 'First submission',
        idempotencyKey: $idempKey
    );
    assertCondition($idempTx1->id === $idempTx2->id, "Duplicate submission with identical payload returns original transaction");
    assertCondition($idempTx1->trx === $idempTx2->trx, "Trx codes match identically");

    $threwMismatch = false;
    try {
        $service->grantMiles(
            user: $user,
            amount: 999, // Mismatch
            source: 'promotional',
            remark: 'Tampered submission',
            idempotencyKey: $idempKey
        );
    } catch (IdempotencyConflictException $e) {
        $threwMismatch = true;
    }
    assertCondition($threwMismatch, "Throws IdempotencyConflictException when payload differs");

    echo "\nScenario 9: Audit Safety & Deletion Blocker\n";
    $blockReason = $user->deleteBlockReason();
    assertCondition(!is_null($blockReason), "User with mileage transactions cannot be deleted");
    assertCondition(str_contains($blockReason, 'cannot be deleted'), "Message instructs admin to ban/deactivate instead");

    echo "\nScenario 10: Miles Permission Semantics (4 Distinct States)\n";
    // State 1: null permissions -> default to enabled
    $user->dashboard_permissions = null;
    $user->save();
    assertCondition(userCanMiles($user) === true, "State 1 (null permissions): Miles is ENABLED");
    assertCondition(userDashboardCan('miles', $user) === true, "userDashboardCan('miles') returns true");

    // State 2: empty array [] -> explicitly disabled; does NOT fall back
    $user->dashboard_permissions = [];
    $user->save();
    assertCondition(userCanMiles($user) === false, "State 2 (empty array []): Miles is DISABLED (no fallback)");
    assertCondition(userDashboardCan('miles', $user) === false, "userDashboardCan('miles') returns false");

    // State 3: custom array with 'miles' -> enabled
    $user->dashboard_permissions = ['wallet_balance', 'miles'];
    $user->save();
    assertCondition(userCanMiles($user) === true, "State 3 (custom array with 'miles'): Miles is ENABLED");
    assertCondition(userDashboardCan('wallet_balance', $user) === true, "Unrelated widget 'wallet_balance' is ENABLED");
    assertCondition(userDashboardCan('loyalty_points', $user) === false, "Unrelated widget 'loyalty_points' is DISABLED");

    // State 4: custom array without 'miles' -> disabled
    $user->dashboard_permissions = ['wallet_balance', 'loyalty_points'];
    $user->save();
    assertCondition(userCanMiles($user) === false, "State 4 (custom array without 'miles'): Miles is DISABLED");
    assertCondition(userDashboardCan('wallet_balance', $user) === true, "Unrelated widget 'wallet_balance' remains ENABLED");

    // State 5: Malformed / non-array data fails closed
    $user->dashboard_permissions = 'corrupted_string_value';
    $user->save();
    assertCondition(userCanMiles($user) === false, "Malformed string fails closed (DISABLED)");
    $user->dashboard_permissions = false;
    $user->save();
    assertCondition(userCanMiles($user) === false, "Malformed boolean false fails closed (DISABLED)");

    echo "\nScenario 11: Admin & Employee Authorization Boundaries\n";
    // 1. Root admin has full permissions
    assertCondition(adminCanMiles('view', $admin) === true, "Root Admin can view miles");
    assertCondition(adminCanMiles('grant', $admin) === true, "Root Admin can grant miles");
    assertCondition(adminCanMiles('deduct', $admin) === true, "Root Admin can deduct miles");
    assertCondition(adminCanMiles('reverse', $admin) === true, "Root Admin can reverse miles");

    // 2. Employee with NO permissions has ZERO miles access
    $emp = new \App\Models\Employee();
    $emp->firstname = 'Staff';
    $emp->lastname = 'User';
    $emp->username = 'staff_' . uniqid();
    $emp->email = 'staff_' . uniqid() . '@example.com';
    $emp->password = bcrypt('password');
    $emp->page_permissions = [];
    $emp->user_permissions = [];
    $emp->save();

    assertCondition(adminCanMiles('view', $emp) === false, "Unprivileged Employee CANNOT view miles");
    assertCondition(adminCanMiles('grant', $emp) === false, "Unprivileged Employee CANNOT grant miles");
    assertCondition(adminCanMiles('deduct', $emp) === false, "Unprivileged Employee CANNOT deduct miles");
    assertCondition(adminCanMiles('reverse', $emp) === false, "Unprivileged Employee CANNOT reverse miles");

    // 3. Employee with VIEW-ONLY page_permission CANNOT mutate
    $emp->page_permissions = ['miles'];
    $emp->save();
    assertCondition(adminCanMiles('view', $emp) === true, "Privileged Employee can view miles");
    assertCondition(adminCanMiles('grant', $emp) === false, "View-only Employee CANNOT grant miles (No privilege escalation)");
    assertCondition(adminCanMiles('deduct', $emp) === false, "View-only Employee CANNOT deduct miles");
    assertCondition(adminCanMiles('reverse', $emp) === false, "View-only Employee CANNOT reverse miles");

    // 4. Employee with GRANT-ONLY user_permission CANNOT deduct or reverse
    $emp->user_permissions = ['miles_grant'];
    $emp->save();
    assertCondition(adminCanMiles('grant', $emp) === true, "Employee with miles_grant can grant miles");
    assertCondition(adminCanMiles('deduct', $emp) === false, "Grant-only Employee CANNOT deduct miles");
    assertCondition(adminCanMiles('reverse', $emp) === false, "Grant-only Employee CANNOT reverse miles");

    echo "\nScenario 12: Unrelated Financial Balances Remain Untouched\n";
    $user->refresh();
    assertCondition((float)$user->balance === 250.00, "Wallet balance remained exactly 250.00");
    assertCondition((int)$user->membership_points_balance === 0, "Loyalty points balance remained 0");
    assertCondition((float)$user->cashback_balance === 0.0, "Cashback balance remained 0.0");

} catch (\Throwable $e) {
    echo "\nUNEXPECTED EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $failed++;
} finally {
    // Completely rollback transaction so the imported production snapshot is 100% pristine
    DB::rollBack();
    echo "\n[CLEANUP] Database rolled back cleanly. No testing records persisted.\n";
}

echo "\n========================================================\n";
echo "   Verification Results: $passed Passed | $failed Failed\n";
echo "========================================================\n";

exit($failed > 0 ? 1 : 0);
