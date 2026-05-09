<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$gs = App\Models\GeneralSetting::first();
$p = $gs->mail_config->password;

echo "Password field exists: " . (isset($p) ? "YES" : "NO") . "\n";
echo "Changed from TEMP_TEST_VALUE_DO_NOT_USE: " . ($p !== 'TEMP_TEST_VALUE_DO_NOT_USE' ? 'YES' : 'NO') . "\n";
echo "Password length > 0: " . (strlen($p) > 0 ? "YES" : "NO") . "\n";
