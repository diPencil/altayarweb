<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\ManageMilesController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\User\MembershipController;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPointTransaction;
use App\Models\User;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "========================================================\n";
echo "   AltayarVIP Miles Subsystem — Phase 4 E2E Audit       \n";
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
    // ----------------------------------------------------
    // 0. Setup Fixtures
    // ----------------------------------------------------
    $userA = new User();
    $userA->firstname = 'Customer';
    $userA->lastname = 'Alpha';
    $userA->username = 'cust_a_' . Str::random(5);
    $userA->email = $userA->username . '@altayarvip.test';
    $userA->password = bcrypt('secret');
    $userA->status = 1;
    $userA->balance = 750.50; // Wallet
    $userA->dashboard_permissions = null; // Default enabled
    $userA->save();

    MembershipPointTransaction::create([
        'user_id' => $userA->id,
        'trx' => getTrx(),
        'type' => 'earned',
        'points' => 350,
        'balance_after' => 350,
        'remark' => 'User A points fixture',
    ]);

    MembershipCashbackTransaction::create([
        'user_id' => $userA->id,
        'trx' => getTrx(),
        'type' => 'earned',
        'amount' => 125.00,
        'balance_after' => 125.00,
        'remark' => 'User A cashback fixture',
    ]);

    $userB = new User();
    $userB->firstname = 'Customer';
    $userB->lastname = 'Beta';
    $userB->username = 'cust_b_' . Str::random(5);
    $userB->email = $userB->username . '@altayarvip.test';
    $userB->password = bcrypt('secret');
    $userB->status = 1;
    $userB->balance = 100.00;
    $userB->dashboard_permissions = null; // Default enabled
    $userB->save();

    $rootAdmin = new Admin();
    $rootAdmin->name = 'System Admin';
    $rootAdmin->username = 'sysadmin_' . Str::random(4);
    $rootAdmin->email = $rootAdmin->username . '@altayarvip.test';
    $rootAdmin->password = bcrypt('secret');
    $rootAdmin->save();

    // Authenticate admin in 'admin' guard for controller requests
    Auth::guard('admin')->login($rootAdmin);

    $milesService = app(MilesService::class);
    $adminMilesController = new ManageMilesController($milesService);
    $adminUsersController = new ManageUsersController();
    $userMembershipController = new MembershipController();

    // ----------------------------------------------------
    // Step 1: Admin selects Customer A
    // ----------------------------------------------------
    echo "Step 1: Admin selects Customer A (#{$userA->id} - {$userA->username})\n";
    assertCondition($userA->id > 0, "Customer A exists in database");
    assertCondition($userA->miles_balance === 0, "Customer A initial miles balance is 0");

    // ----------------------------------------------------
    // Step 2: Admin grants 1,000 Miles
    // ----------------------------------------------------
    echo "\nStep 2: Admin grants 1,000 Miles via ManageMilesController\n";
    $grantKey = 'e2e_grant_' . Str::random(8);
    $reqGrant = Request::create(route('admin.membership.miles.operate'), 'POST', [
        'user_id' => $userA->id,
        'act' => 'grant',
        'amount' => 1000,
        'source' => 'admin_e2e',
        'reference_id' => 'BOOK-7788',
        'remark' => 'Loyalty flight booking award',
        'idempotency_key' => $grantKey,
    ]);
    $resGrant = $adminMilesController->operate($reqGrant);
    assertCondition($resGrant->isRedirection(), "Admin grant operation completed with redirect");

    // ----------------------------------------------------
    // Step 3: Customer A sees Total Miles = 1,000
    // ----------------------------------------------------
    echo "\nStep 3: Customer A sees Total Miles = 1,000\n";
    $userA->refresh();
    assertCondition($userA->miles_balance === 1000, "Customer A miles_balance is exactly 1,000");
    assertCondition(userDashboardCan('miles', $userA) === true, "Customer A dashboard overview displays Miles card");

    // ----------------------------------------------------
    // Step 4: Admin deducts 200 Miles
    // ----------------------------------------------------
    echo "\nStep 4: Admin deducts 200 Miles via ManageMilesController\n";
    $deductKey = 'e2e_deduct_' . Str::random(8);
    $reqDeduct = Request::create(route('admin.membership.miles.operate'), 'POST', [
        'user_id' => $userA->id,
        'act' => 'deduct',
        'amount' => 200,
        'source' => 'admin_e2e',
        'reference_id' => 'HOTEL-DISC-01',
        'remark' => 'Redeemed 200 miles for hotel discount',
        'idempotency_key' => $deductKey,
    ]);
    $resDeduct = $adminMilesController->operate($reqDeduct);
    assertCondition($resDeduct->isRedirection(), "Admin deduct operation completed with redirect");

    // ----------------------------------------------------
    // Step 5: Customer A sees Total Miles = 800
    // ----------------------------------------------------
    echo "\nStep 5: Customer A sees Total Miles = 800\n";
    $userA->refresh();
    assertCondition($userA->miles_balance === 800, "Customer A miles_balance is exactly 800 (1,000 - 200)");

    // ----------------------------------------------------
    // Step 6: Customer A sees both operations in Miles History
    // ----------------------------------------------------
    echo "\nStep 6: Customer A sees both operations in Miles History\n";
    Auth::guard('web')->login($userA);
    $history = UserMilesTransaction::where('user_id', $userA->id)->orderBy('id', 'asc')->get();
    assertCondition($history->count() === 2, "Customer A ledger contains exactly 2 operations");
    assertCondition($history[0]->type === 'credit' && $history[0]->miles === 1000 && $history[0]->balance_after === 1000, "Entry 1: Credit +1000 with balance_after 1000");
    assertCondition($history[1]->type === 'debit' && $history[1]->miles === 200 && $history[1]->balance_after === 800, "Entry 2: Debit -200 with balance_after 800");

    // ----------------------------------------------------
    // Step 7: Admin reverses the debit
    // ----------------------------------------------------
    echo "\nStep 7: Admin reverses the debit transaction\n";
    $debitTrxId = $history[1]->id;
    $reqReverse = Request::create(route('admin.membership.miles.reverse', $debitTrxId), 'POST', [
        'reason' => 'Booking canceled by hotel - refunding redeemed miles',
    ]);
    $resReverse = $adminMilesController->reverse($reqReverse, $debitTrxId);
    assertCondition($resReverse->isRedirection(), "Admin reversal completed with redirect");

    // ----------------------------------------------------
    // Step 8: Customer A sees Total Miles = 1,000 restored
    // ----------------------------------------------------
    echo "\nStep 8: Customer A sees Total Miles = 1,000 restored\n";
    $userA->refresh();
    assertCondition($userA->miles_balance === 1000, "Customer A balance restored back to 1,000");
    $latestEntry = UserMilesTransaction::where('user_id', $userA->id)->latest('id')->first();
    assertCondition($latestEntry->is_reversal === true, "Compensating transaction created with is_reversal = true");
    assertCondition($latestEntry->type === 'credit' && $latestEntry->direction === 1, "Compensating entry has type credit and direction +1");

    // ----------------------------------------------------
    // Step 9: Admin disables Miles for Customer A
    // ----------------------------------------------------
    echo "\nStep 9: Admin disables Miles for Customer A via ManageUsersController\n";
    $reqDisable = Request::create(route('admin.users.update', $userA->id), 'POST', [
        'firstname' => $userA->firstname,
        'lastname' => $userA->lastname,
        'email' => $userA->email,
        'mobile' => '12345678',
        'country' => 'EG',
        'has_dashboard_permissions' => 1,
        'dashboard_permissions' => ['wallet_balance', 'loyalty_points'], // 'miles' is excluded
    ]);
    $adminUsersController->update($reqDisable, $userA->id);
    $userA->refresh();
    assertCondition(is_array($userA->dashboard_permissions), "dashboard_permissions is persisted as array");
    assertCondition(!in_array('miles', $userA->dashboard_permissions, true), "'miles' is absent from dashboard_permissions");

    // ----------------------------------------------------
    // Step 10: Miles card, sidebar and direct access become unavailable
    // ----------------------------------------------------
    echo "\nStep 10: Miles card, sidebar and direct page access become unavailable\n";
    assertCondition(userCanMiles($userA) === false, "userCanMiles(userA) returns false");
    assertCondition(userDashboardCan('miles', $userA) === false, "userDashboardCan('miles') returns false (card hidden)");

    // Simulate Customer A attempting direct access to user.membership.miles
    Auth::guard('web')->login($userA);
    $directAccessResponse = $userMembershipController->miles();
    assertCondition($directAccessResponse->isRedirection(), "Direct route access is blocked and redirected to dashboard");
    assertCondition($directAccessResponse->getTargetUrl() === route('user.home'), "Redirect targets user.home");

    // ----------------------------------------------------
    // Step 11: Customer B is unaffected
    // ----------------------------------------------------
    echo "\nStep 11: Customer B is unaffected\n";
    assertCondition(userCanMiles($userB) === true, "Customer B userCanMiles returns true (legacy default enabled)");
    assertCondition(userDashboardCan('miles', $userB) === true, "Customer B dashboard overview still shows Miles card");
    assertCondition($userB->miles_balance === 0, "Customer B balance remains isolated at 0");

    // ----------------------------------------------------
    // Step 12: Admin re-enables Miles for Customer A
    // ----------------------------------------------------
    echo "\nStep 12: Admin re-enables Miles for Customer A\n";
    $reqEnable = Request::create(route('admin.users.update', $userA->id), 'POST', [
        'firstname' => $userA->firstname,
        'lastname' => $userA->lastname,
        'email' => $userA->email,
        'mobile' => '12345678',
        'country' => 'EG',
        'has_dashboard_permissions' => 1,
        'dashboard_permissions' => ['wallet_balance', 'loyalty_points', 'miles'], // 'miles' is re-added
    ]);
    $adminUsersController->update($reqEnable, $userA->id);
    $userA->refresh();
    assertCondition(userCanMiles($userA) === true, "userCanMiles(userA) returns true after re-enabling");
    assertCondition(userDashboardCan('miles', $userA) === true, "Customer A dashboard overview card restored");

    // ----------------------------------------------------
    // Step 13: Customer A's original balance and history remain intact
    // ----------------------------------------------------
    echo "\nStep 13: Customer A original balance and history remain intact\n";
    assertCondition($userA->miles_balance === 1000, "Customer A miles balance is still exactly 1,000");
    $totalHistory = UserMilesTransaction::where('user_id', $userA->id)->count();
    assertCondition($totalHistory === 3, "All 3 historical ledger transactions perfectly preserved");

    // ----------------------------------------------------
    // Step 14: Cross-User Data Isolation Verification
    // ----------------------------------------------------
    echo "\nStep 14: Cross-User Data Isolation Verification\n";
    Auth::guard('web')->login($userB);
    $userBTransactions = UserMilesTransaction::where('user_id', $userB->id)->count();
    assertCondition($userBTransactions === 0, "Customer B has 0 transactions (Customer A's ledger never leaks)");

    // ----------------------------------------------------
    // Step 15: Financial Ledgers Isolation
    // ----------------------------------------------------
    echo "\nStep 15: Financial Ledgers Isolation (Wallet, Loyalty, Cashback)\n";
    $userA->refresh();
    assertCondition((float) $userA->balance === 750.50, "Wallet balance remained exactly 750.50");
    assertCondition((int) $userA->membership_points_balance === 350, "Loyalty points remained exactly 350");
    assertCondition((float) $userA->cashback_balance === 125.00, "Cashback balance remained exactly 125.00");

    // ----------------------------------------------------
    // Step 16: Employee Authorization Hardening
    // ----------------------------------------------------
    echo "\nStep 16: Employee Authorization Hardening\n";
    $unprivilegedEmp = new Employee();
    $unprivilegedEmp->username = 'emp_test_' . Str::random(4);
    $unprivilegedEmp->page_permissions = [];
    $unprivilegedEmp->user_permissions = [];

    assertCondition(adminCanMiles('view', $unprivilegedEmp) === false, "Unprivileged employee cannot view");
    assertCondition(adminCanMiles('grant', $unprivilegedEmp) === false, "Unprivileged employee cannot grant");
    assertCondition(adminCanMiles('deduct', $unprivilegedEmp) === false, "Unprivileged employee cannot deduct");
    assertCondition(adminCanMiles('reverse', $unprivilegedEmp) === false, "Unprivileged employee cannot reverse");

} catch (\Throwable $e) {
    echo "\n[ERROR] Exception encountered during E2E test execution: " . $e->getMessage() . "\n";
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
