<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\MilesService;

$userId = (int) ($argv[1] ?? 0);
$amount = (int) ($argv[2] ?? 0);
$remark = (string) ($argv[3] ?? 'Concurrent Worker');
$outputFile = (string) ($argv[4] ?? '');

$result = [
    'pid' => getmypid(),
    'success' => false,
    'error' => null,
    'balance_after' => null,
    'tx_id' => null,
];

try {
    $service = app(MilesService::class);
    $user = User::findOrFail($userId);

    $tx = $service->deductMiles(
        user: $user,
        amount: $amount,
        source: 'manual',
        remark: $remark
    );

    $result['success'] = true;
    $result['balance_after'] = $tx->balance_after;
    $result['tx_id'] = $tx->id;
} catch (\Throwable $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
}

if ($outputFile) {
    file_put_contents($outputFile, json_encode($result));
}

exit($result['success'] ? 0 : 2);
