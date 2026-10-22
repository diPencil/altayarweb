<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\UserMilesTransaction;
use Illuminate\Support\Str;

echo "=================================================================\n";
echo "   Real HTTP Routes & Form Submissions E2E Audit (Port 8001)     \n";
echo "=================================================================\n\n";

$baseUrl = 'http://127.0.0.1:8001';
$cookieJar = tempnam(sys_get_temp_dir(), 'altayar_cookie_');

// 1. Establish Admin Session
echo "Step 1: Authenticating Admin on {$baseUrl}...\n";
$ch = curl_init("{$baseUrl}/local-test-login/admin");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
assertTest("Admin login redirect completed with HTTP 200", $httpCode === 200);

// Extract CSRF token from HTML
preg_match('/name="_token"\s+value="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? null;
assertTest("CSRF token obtained from admin dashboard", !empty($csrfToken));

// 2. Test Insufficient Balance Overdraft Submission
echo "\nStep 2: Submitting Overdraft Deduction (999,999 miles) on User #570...\n";
$ch = curl_init("{$baseUrl}/admin/membership/miles/operate");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $csrfToken,
        'act' => 'deduct',
        'user_id' => 570,
        'amount' => 999999,
        'source' => 'admin',
        'remark' => 'Overdraft attempt test',
        'idempotency_key' => (string) Str::uuid(),
    ]),
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$overdraftHtml = curl_exec($ch);
$overdraftCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
assertTest("Overdraft request processed with HTTP 200", $overdraftCode === 200);
assertTest("UI displays Insufficient balance error notification", str_contains($overdraftHtml, 'Insufficient') || str_contains($overdraftHtml, 'غير كاف'));

// 3. Test Reversal Submission on Transaction #90
echo "\nStep 3: Submitting Reversal of Transaction #90 (200 miles deduction)...\n";
$tx90 = UserMilesTransaction::findOrFail(90);
$user570 = User::findOrFail(570);
$balanceBeforeReversal = $user570->miles_balance;
echo "Balance before reversal: {$balanceBeforeReversal} miles\n";

$ch = curl_init("{$baseUrl}/admin/membership/miles/reverse/90");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $csrfToken,
        'reason' => 'HTTP reversal audit test',
        'idempotency_key' => (string) Str::uuid(),
    ]),
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$reversalHtml = curl_exec($ch);
$reversalCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

assertTest("Reversal request processed with HTTP 200", $reversalCode === 200);
$tx90Fresh = UserMilesTransaction::findOrFail(90);
assertTest("Original transaction #90 is_reversed marked true", $tx90Fresh->is_reversed == 1);
assertTest("Original transaction #90 reversed_at timestamp recorded", !empty($tx90Fresh->reversed_at));

$compensatingTx = UserMilesTransaction::where('reversal_of_id', 90)->first();
assertTest("Compensating reversal transaction exists in DB", $compensatingTx !== null);
assertTest("Compensating transaction type is credit (+)", $compensatingTx->type === 'credit');
assertTest("Compensating transaction direction is +1", (int)$compensatingTx->direction === 1);
assertTest("Compensating transaction miles is 200", (int)$compensatingTx->miles === 200);
assertTest("Balance restored back to 500 miles", $user570->fresh()->miles_balance === 500);

// 4. Test Duplicate Submission / Strict Idempotency
echo "\nStep 4: Testing Duplicate Submission with identical Idempotency Key...\n";
$idempotencyKey = (string) Str::uuid();
$payload = [
    '_token' => $csrfToken,
    'act' => 'grant',
    'user_id' => 570,
    'amount' => 100,
    'source' => 'admin',
    'remark' => 'Idempotency test grant',
    'idempotency_key' => $idempotencyKey,
];

// First submission
$ch = curl_init("{$baseUrl}/admin/membership/miles/operate");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($payload),
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
curl_exec($ch);
curl_close($ch);

$countAfterFirst = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->count();
assertTest("First submission created exactly 1 transaction", $countAfterFirst === 1);
$firstTx = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();

// Duplicate submission with identical payload
$ch = curl_init("{$baseUrl}/admin/membership/miles/operate");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($payload),
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
curl_exec($ch);
curl_close($ch);

$countAfterSecond = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->count();
assertTest("Duplicate submission did NOT create duplicate row (count remains 1)", $countAfterSecond === 1);

// 5. Test Customer Permission Save and Reload
echo "\nStep 5: Testing Customer Miles Permission Save & Route Guards...\n";

// Disable Miles for User #570
$user570->dashboard_permissions = ['wallet_balance', 'loyalty_points']; // Excludes 'miles'
$user570->save();
assertTest("Miles permission disabled on User #570", !in_array('miles', $user570->fresh()->dashboard_permissions, true));

// Test customer login as User #570
$userCookieJar = tempnam(sys_get_temp_dir(), 'altayar_user_cookie_');
$ch = curl_init("{$baseUrl}/local-test-login/user/570");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $userCookieJar,
    CURLOPT_COOKIEFILE => $userCookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$dashboardHtml = curl_exec($ch);
curl_close($ch);

// Miles card must be hidden
assertTest("Customer dashboard overview does NOT show 'Total Miles' card", !str_contains($dashboardHtml, 'Total Miles') && !str_contains($dashboardHtml, 'إجمالي الأميال'));

// Direct route to miles history must be blocked
$ch = curl_init("{$baseUrl}/user/membership/miles");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $userCookieJar,
    CURLOPT_COOKIEFILE => $userCookieJar,
    CURLOPT_FOLLOWLOCATION => false, // do not follow redirect to catch 302
]);
curl_exec($ch);
$milesRedirectCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectTarget = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

assertTest("Direct customer access to /user/membership/miles returns 302 redirect", $milesRedirectCode === 302);
assertTest("Redirect target is user dashboard", str_contains($redirectTarget, '/user/dashboard') || str_contains($redirectTarget, '/user/home'));

// Re-enable Miles for User #570
$user570->dashboard_permissions = ['wallet_balance', 'loyalty_points', 'miles'];
$user570->save();
assertTest("Miles permission re-enabled on User #570", in_array('miles', $user570->fresh()->dashboard_permissions, true));

// Customer dashboard must now show Miles card
$ch = curl_init("{$baseUrl}/user/dashboard");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $userCookieJar,
    CURLOPT_COOKIEFILE => $userCookieJar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$dashboardRestoredHtml = curl_exec($ch);
curl_close($ch);
assertTest("Customer dashboard overview restores 'Total Miles' card", str_contains($dashboardRestoredHtml, 'Total Miles') || str_contains($dashboardRestoredHtml, 'إجمالي الأميال'));

// Direct route to miles history is now allowed
$ch = curl_init("{$baseUrl}/user/membership/miles");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $userCookieJar,
    CURLOPT_COOKIEFILE => $userCookieJar,
    CURLOPT_FOLLOWLOCATION => false,
]);
$milesPageHtml = curl_exec($ch);
$milesPageCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
assertTest("Customer miles history route now accessible with HTTP 200", $milesPageCode === 200);

// Cleanup test transactions created during this script
echo "\n[CLEANUP] Removing script testing transactions and cookies...\n";
if ($firstTx) {
    $firstTx->delete();
}
if ($compensatingTx) {
    $compensatingTx->delete();
}
$tx90->is_reversed = 0;
$tx90->reversed_at = null;
$tx90->save();
$user570->dashboard_permissions = null;
$user570->save();
@unlink($cookieJar);
@unlink($userCookieJar);

echo "[DONE] All Real HTTP & Form Submission tests PASSED!\n";

function assertTest(string $description, bool $condition): void {
    if ($condition) {
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
        throw new RuntimeException("Assertion failed: {$description}");
    }
}
