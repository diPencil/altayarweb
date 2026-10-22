<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Illuminate\Support\Facades\DB;

echo "=========================================================\n";
echo "  Real Concurrency Test: 2 Independent OS Processes     \n";
echo "=========================================================\n\n";

$service = app(MilesService::class);

// 1. Create a disposable test user and commit to database so child processes can access it
$user = new User();
$user->firstname = 'Parallel';
$user->lastname = 'RaceTester';
$user->username = 'real_race_' . uniqid();
$user->email = 'real_race_' . uniqid() . '@example.com';
$user->mobile = '+2010' . rand(10000000, 99999999);
$user->password = bcrypt('password');
$user->balance = 50.00;
$user->status = 1;
$user->ev = 1;
$user->sv = 1;
$user->save();

$userId = $user->id;

// Initial Grant: 800 miles
$initialTx = $service->grantMiles($user, 800, 'manual', 'Initial race balance');
echo "[SETUP] Created test user #$userId with initial balance: 800 miles (Trx: {$initialTx->trx})\n";

$out1 = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'miles_race_out1_' . uniqid() . '.json';
$out2 = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'miles_race_out2_' . uniqid() . '.json';

$workerPath = escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'worker_miles_concurrent.php');
$phpBinary = escapeshellarg(PHP_BINARY);

$cmd1 = "$phpBinary $workerPath $userId 500 \"Parallel Worker 1\" " . escapeshellarg($out1);
$cmd2 = "$phpBinary $workerPath $userId 500 \"Parallel Worker 2\" " . escapeshellarg($out2);

$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$passed = false;
try {
    echo "[LAUNCH] Spawning Process 1 (attempting to deduct 500 miles)...\n";
    $proc1 = proc_open($cmd1, $descriptors, $pipes1);

    echo "[LAUNCH] Spawning Process 2 (attempting to deduct 500 miles) SIMULTANEOUSLY...\n";
    $proc2 = proc_open($cmd2, $descriptors, $pipes2);

    // Wait for both independent operating system processes to finish
    $exitCode1 = proc_close($proc1);
    $exitCode2 = proc_close($proc2);

    echo "[COMPLETE] Process 1 finished with exit code $exitCode1\n";
    echo "[COMPLETE] Process 2 finished with exit code $exitCode2\n\n";

    $res1 = file_exists($out1) ? json_decode(file_get_contents($out1), true) : null;
    $res2 = file_exists($out2) ? json_decode(file_get_contents($out2), true) : null;

    @unlink($out1);
    @unlink($out2);

    echo "Process 1 Report: " . json_encode($res1) . "\n";
    echo "Process 2 Report: " . json_encode($res2) . "\n\n";

    // Inspect the final authoritative database state
    $transactions = UserMilesTransaction::where('user_id', $userId)->orderBy('id', 'asc')->get();
    $finalBalance = $service->calculateAvailableBalance($userId);

    echo "[DATABASE VERIFICATION]\n";
    echo "Total transactions in DB for user: " . $transactions->count() . "\n";
    foreach ($transactions as $t) {
        echo "  - ID {$t->id} | {$t->type} | direction: {$t->direction} | miles: {$t->miles} | balance_after: {$t->balance_after} | remark: {$t->remark}\n";
    }
    echo "Final authoritative balance: $finalBalance miles\n\n";

    // Evaluation assertions
    $successCount = ($res1['success'] ? 1 : 0) + ($res2['success'] ? 1 : 0);
    $failureCount = ((!$res1['success']) ? 1 : 0) + ((!$res2['success']) ? 1 : 0);

    $passed = true;

    if ($successCount === 1) {
        echo "  [PASS] Exactly ONE process succeeded\n";
    } else {
        echo "  [FAIL] Expected 1 success, got $successCount\n";
        $passed = false;
    }

    if ($failureCount === 1) {
        echo "  [PASS] Exactly ONE process was safely rejected with insufficient balance\n";
    } else {
        echo "  [FAIL] Expected 1 failure, got $failureCount\n";
        $passed = false;
    }

    if ($finalBalance === 300) {
        echo "  [PASS] Final ledger balance is exactly 300 (800 - 500), never negative\n";
    } else {
        echo "  [FAIL] Expected final balance 300, got $finalBalance\n";
        $passed = false;
    }

    if ($transactions->count() === 2) {
        echo "  [PASS] Exactly 2 transactions recorded (1 initial credit + 1 deduction), zero duplicate entries\n";
    } else {
        echo "  [FAIL] Expected 2 transactions in ledger, got " . $transactions->count() . "\n";
        $passed = false;
    }
} finally {
    // Clean up only the disposable test user created in this run
    UserMilesTransaction::where('user_id', $userId)->delete();
    $user->delete();
    @unlink($out1);
    @unlink($out2);
    echo "\n[CLEANUP] Disposable test user #$userId and transactions purged safely from database.\n";
}

echo "=========================================================\n";
if ($passed) {
    echo "  Real Multi-Process Concurrency Test: 100% SUCCESS\n";
    echo "=========================================================\n";
    exit(0);
} else {
    echo "  Real Multi-Process Concurrency Test: FAILED\n";
    echo "=========================================================\n";
    exit(1);
}
