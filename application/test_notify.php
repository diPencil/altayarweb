<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notify\Email;

$user = User::find(256);
$email = new Email();
$email->subject = 'Simulated Test';
$email->message = 'This is a simulated message body.';
$email->user = $user;

echo "DEBUG: Message property set to: " . $email->message . "\n";
$msg = $email->getMessage();
echo "DEBUG: getMessage() returned type: " . gettype($msg) . "\n";
echo "DEBUG: getMessage() returned value length: " . strlen($msg) . "\n";
echo "DEBUG: receiverName: " . $email->receiverName . "\n";
echo "DEBUG: email: " . $email->email . "\n";

if ($msg) {
    echo "DEBUG: Final message content starts with: " . substr($msg, 0, 50) . "...\n";
} else {
    echo "ERROR: getMessage() returned empty/false!\n";
}

$general = gs();
echo "DEBUG: Global Email Enabled (en): " . $general->en . "\n";

echo "DEBUG: Attempting to send...\n";
try {
    $email->send();
    echo "SUCCESS: send() logic completed.\n";
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
