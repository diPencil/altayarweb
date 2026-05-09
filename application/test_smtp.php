<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PHPMailer\PHPMailer\PHPMailer;
use App\Models\GeneralSetting;

$recipient = 'info@altayarvip.com';
$subject = 'AltayarVIP Direct SMTP Confirmation';
$body = 'This is a direct SMTP confirmation email sent at ' . date('Y-m-d H:i:s');

echo "DEBUG: Starting SMTP test to $recipient\n";

try {
    $mail = new PHPMailer(true);
    $general = GeneralSetting::first();
    $config = $general->mail_config;

    echo "DEBUG: SMTP Host: {$config->host}\n";
    echo "DEBUG: SMTP Port: {$config->port}\n";
    echo "DEBUG: SMTP Username: {$config->username}\n";
    echo "DEBUG: SMTP Encryption: {$config->enc}\n";

    $mail->isSMTP();
    $mail->Host       = $config->host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $config->username;
    $mail->Password   = $config->password;
    if ($config->enc == 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }
    $mail->Port       = $config->port;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($general->email_from, $general->site_name);
    $mail->addAddress($recipient, 'AltayarVIP Admin');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = "<b>$body</b>";

    echo "DEBUG: Attempting to send...\n";
    $result = $mail->send();
    echo "SUCCESS: PHPMailer returned " . ($result ? "true" : "false") . "\n";
    echo "SUCCESS: Email sent to $recipient\n";
} catch (Exception $e) {
    echo "ERROR: Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}
