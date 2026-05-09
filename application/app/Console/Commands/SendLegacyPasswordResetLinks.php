<?php

namespace App\Console\Commands;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notify\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SendLegacyPasswordResetLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:send-legacy-password-reset-links 
                            {--send : Actually send the emails} 
                            {--dry-run : Only show what would be done (default)}
                            {--user-id= : Send only to a specific user ID for testing}
                            {--email= : Send only to a specific email for testing}
                            {--preview-to= : Send all emails to this specific address for testing}
                            {--locale= : Set the language locale (e.g., ar)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send password reset links to legacy imported users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $send = $this->option('send');
        $dryRun = !$send;
        $targetUserId = $this->option('user-id');
        $targetEmail = $this->option('email');
        $previewTo = $this->option('preview-to');
        $locale = $this->option('locale') ?: 'en';

        if ($dryRun) {
            $this->info("DRY RUN MODE: No emails will be sent.");
        } else {
            $this->warn("SEND MODE: Emails will be sent to legacy users.");
        }

        if ($previewTo) {
            $this->info("PREVIEW MODE: All emails will be sent to: $previewTo");
        }

        if ($locale == 'ar') {
            $this->info("LOCALE: Arabic (ar) - RTL mode enabled.");
        }

        // Identify legacy imported users
        $query = User::whereNotNull('legacy_user_id');

        if ($targetUserId) {
            $query->where('id', $targetUserId);
            $this->info("Filtering for User ID: $targetUserId");
        }

        if ($targetEmail) {
            $query->where('email', $targetEmail);
            $this->info("Filtering for Email: $targetEmail");
        }

        $totalLegacy = $query->count();
        $users = $query->get();

        $eligibleCount = 0;
        $skippedMissingEmail = 0;
        $skippedInvalidEmail = 0;
        $skippedDuplicateEmail = 0;
        
        $emailsSeen = [];
        $recipients = [];

        foreach ($users as $user) {
            $email = trim($user->email);

            if (empty($email)) {
                $skippedMissingEmail++;
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedInvalidEmail++;
                continue;
            }

            if (isset($emailsSeen[$email])) {
                $skippedDuplicateEmail++;
                continue;
            }

            $emailsSeen[$email] = true;
            $eligibleCount++;
            $recipients[] = $user;
        }

        $this->info("Total legacy imported users: $totalLegacy");
        $this->info("Users with valid emails: $eligibleCount");
        $this->line("Users skipped (missing email): $skippedMissingEmail", 'comment');
        $this->line("Users skipped (invalid email): $skippedInvalidEmail", 'comment');
        $this->line("Users skipped (duplicate email): $skippedDuplicateEmail", 'comment');

        // Sample recipients
        $this->newLine();
        $this->info("Sample recipients (up to 5):");
        $samples = array_slice($recipients, 0, 5);
        $sampleData = [];
        foreach ($samples as $s) {
            $email = trim($s->email);
            $atIndex = strpos($email, '@');
            $maskedEmail = substr($email, 0, min(3, $atIndex)) . '****' . substr($email, $atIndex);

            $sampleData[] = [
                'id' => $s->id,
                'username' => $s->username,
                'email' => $maskedEmail,
                'legacy_user_id' => $s->legacy_user_id
            ];
        }
        $this->table(['ID', 'Username', 'Email', 'Legacy ID'], $sampleData);

        // Email details
        $this->newLine();
        $subject = ($locale == 'ar') ? "تعيين كلمة المرور لحسابك في AltayarVIP" : "Set Your Password for Your AltayarVIP Account";
        $this->info("Email Subject: $subject");
        $this->info("Reset Route: user.password.reset");
        
        $baseUrl = "https://altayarvip.com";
        $dummyToken = "RESET_TOKEN_HIDDEN_FOR_SAFETY";
        $maskedUrl = "$baseUrl/user/password/reset/$dummyToken?email=test@example.com";
        $this->info("Masked Reset URL Example: $maskedUrl");

        if ($dryRun) {
            $this->newLine();
            $this->warn("Dry run finished. No emails were sent.");
            $this->info("To send emails, run: php artisan users:send-legacy-password-reset-links --send");
            return 0;
        }

        // Send mode
        $this->info("Starting to send emails...");
        $sentCount = 0;
        $failedCount = 0;
        $report = [];
        $report[] = ['user_id', 'username', 'email', 'legacy_user_id', 'status', 'reason', 'sent_at'];

        $bar = $this->output->createProgressBar(count($recipients));
        $bar->start();

        foreach ($recipients as $user) {
            try {
                // Generate token
                $token = Str::random(40);
                
                // Save to password_resets
                PasswordReset::where('email', $user->email)->delete();
                $reset = new PasswordReset();
                $reset->email = $user->email;
                $reset->token = $token;
                $reset->created_at = now();
                $reset->save();

                // Prepare link
                $link = "$baseUrl/user/password/reset/$token?email=" . urlencode($user->email);

                // Prepare Email Content
                $name = $user->firstname ? $user->firstname . ' ' . $user->lastname : $user->username;
                
                // Prepare Email Content
                $name = $user->firstname ? $user->firstname . ' ' . $user->lastname : $user->username;
                $subject = ($locale == 'ar') ? "تعيين كلمة المرور لحسابك في AltayarVIP" : "Set Your Password for Your AltayarVIP Account";
                
                if ($locale == 'ar') {
                    $body = '<div dir="rtl" style="text-align: right; font-family: sans-serif;">';
                    $body .= "عزيزي {$name}،<br><br>";
                    $body .= "يسعدنا إبلاغك بأنه تم نقل حسابك في AltayarVIP بنجاح إلى النظام الجديد.<br><br>";
                    $body .= "للدخول إلى حسابك، يرجى تعيين كلمة مرور جديدة من خلال الزر التالي:<br><br>";
                    $body .= '<div style="text-align: center; margin: 30px 0;">';
                    $body .= '<a href="' . $link . '" style="background-color: #d11218; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block;">تعيين كلمة المرور</a>';
                    $body .= '</div>';
                    $body .= "حرصًا على أمان حسابك، هذا الرابط خاص بك فقط ولا يجب مشاركته مع أي شخص.<br><br>";
                    $body .= "بعد تعيين كلمة المرور، ستتمكن من تسجيل الدخول والوصول إلى عضويتك، حجوزاتك، المميزات الخاصة بك، النقاط، المحفظة، وباقي خدمات حسابك.<br><br>";
                    $body .= "إذا لم يعمل الزر، يمكنك نسخ الرابط التالي وفتحه في المتصفح:<br>";
                    $body .= '<a href="' . $link . '">' . $link . '</a><br><br>';
                    $body .= "إذا كنت بحاجة إلى أي مساعدة، يرجى التواصل مع دعم AltayarVIP.<br><br>";
                    $body .= "مع خالص التحية،<br>فريق AltayarVIP";
                    $body .= '</div>';
                } else {
                    $body = "Dear $name,<br><br>";
                    $body .= "We are pleased to inform you that your AltayarVIP account has been successfully moved to our new system.<br><br>";
                    $body .= "To access your account, please set a new password using the button below.<br><br>";
                    $body .= '<div style="text-align: center; margin: 30px 0;">';
                    $body .= '<a href="' . $link . '" style="background-color: #d11218; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block;">Set Your Password</a>';
                    $body .= '</div>';
                    $body .= "For your security, this link is private and should not be shared with anyone.<br><br>";
                    $body .= "After setting your password, you will be able to log in and access your membership, bookings, benefits, points, wallet, and other account services.<br><br>";
                    $body .= "If the button above does not work, copy and paste this link:<br>";
                    $body .= '<a href="' . $link . '">' . $link . '</a><br><br>';
                    $body .= "If you need help, please contact AltayarVIP support.<br><br>";
                    $body .= "Best regards,<br>AltayarVIP Team";
                }

                // Send via project Notify/Email
                $email = new Email();
                $email->subject = $subject;
                $email->message = $body;

                // Explicitly set shortcodes for global template replacement
                $general = gs();
                $footer = ($locale == 'ar') ? "جميع الحقوق محفوظة.<br>منصة سفر وحجوزات مميزة<br>فريق AltayarVIP" : "All rights reserved.<br>Premium Travel & Booking Platform<br>AltayarVIP Team";
                
                $email->shortCodes = [
                    'site_name' => 'AltayarVIP',
                    'year' => date('Y'),
                    'logo_url' => 'https://www.altayarvip.com/assets/images/general/logo.png',
                    'fullname' => $name,
                    'username' => $user->username,
                ];

                if ($previewTo) {
                    // Clone user to avoid modifying the real user in memory or DB
                    $userForNotify = clone $user;
                    $userForNotify->email = $previewTo;
                    $email->user = $userForNotify;
                    $email->receiverName = "Preview Recipient ($name)";
                } else {
                    $email->user = $user;
                }

                $email->send();

                $sentCount++;
                $status = $previewTo ? "sent (preview to $previewTo)" : "sent";
                $report[] = [$user->id, $user->username, $user->email, $user->legacy_user_id, $status, '', now()];
            } catch (\Exception $e) {
                $failedCount++;
                $report[] = [$user->id, $user->username, $user->email, $user->legacy_user_id, 'failed', $e->getMessage(), now()];
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // Write CSV report
        $reportDir = storage_path('app/imports');
        if (!File::isDirectory($reportDir)) {
            File::makeDirectory($reportDir, 0755, true);
        }
        $reportPath = $reportDir . '/legacy_password_reset_email_report.csv';
        $file = fopen($reportPath, 'w');
        foreach ($report as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        $this->info("Emails sent: $sentCount");
        $this->error("Failed: $failedCount");
        $this->info("Report saved to: $reportPath");

        return 0;
    }
}
