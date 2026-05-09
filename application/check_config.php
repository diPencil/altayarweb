<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GeneralSetting;

$gs = GeneralSetting::first();
echo "Email From: " . $gs->email_from . "\n";
echo "SMTP Username: " . $gs->mail_config->username . "\n";
echo "SMTP Host: " . $gs->mail_config->host . "\n";
