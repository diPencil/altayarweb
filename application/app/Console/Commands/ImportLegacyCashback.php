<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MembershipCashbackTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportLegacyCashback extends Command
{
    protected $signature = 'cashback:import-legacy-membership-transactions {--dry-run}';
    protected $description = 'Import legacy membership payment values as cashback from CSV';

    public function handle()
    {
        $file = storage_path('app/imports/legacy_membership_cashback.csv');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info("=== DRY RUN MODE ===");
        }

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        
        // Remove BOM if present
        if ($headers && isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        }
        
        $summary = [
            'total_rows' => 0,
            'users_found' => 0,
            'users_not_found' => 0,
            'with_cashback' => 0,
            'zero_amount_skipped' => 0,
            'already_imported' => 0,
            'imported_count' => 0,
            'total_amount' => 0,
            'failed' => 0,
        ];

        $unmatched = [];
        $samples = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            $summary['total_rows']++;

            $legacyUserId = trim($data['legacy_user_id'] ?? '');
            $email = trim($data['email'] ?? '');
            $amount = (float) ($data['cashback_amount'] ?? 0);

            if ($amount <= 0) {
                $summary['zero_amount_skipped']++;
                continue;
            }

            if (empty($legacyUserId) && empty($email)) {
                $summary['failed']++;
                continue;
            }

            $user = null;
            if (!empty($legacyUserId)) {
                $user = User::where('legacy_user_id', $legacyUserId)->first();
            }

            if (!$user && !empty($email)) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $summary['users_not_found']++;
                $unmatched[] = [
                    'legacy_user_id' => $legacyUserId,
                    'email' => $email,
                    'customer_name' => $data['customer_name'] ?? '',
                    'amount' => $amount
                ];
                continue;
            }

            $summary['users_found']++;
            $summary['with_cashback']++;

            // Check idempotency
            $exists = MembershipCashbackTransaction::where('user_id', $user->id)
                ->where('remark', 'legacy_membership_transactions_cashback')
                ->exists();

            if ($exists) {
                $summary['already_imported']++;
                continue;
            }

            $summary['total_amount'] += $amount;

            if (!$dryRun) {
                try {
                    DB::beginTransaction();
                    
                    $currentBalance = (float) $user->cashback_balance;
                    $trx = 'CB-LEGACY-' . ($legacyUserId ?? $user->id) . '-' . time() . '-' . mt_rand(100, 999);

                    MembershipCashbackTransaction::create([
                        'user_id' => $user->id,
                        'tour_booking_id' => null,
                        'trx' => $trx,
                        'type' => 'earned',
                        'amount' => $amount,
                        'balance_after' => $currentBalance + $amount,
                        'remark' => 'legacy_membership_transactions_cashback',
                        'meta' => [
                            'legacy_import' => true,
                            'source' => 'Old Membership Transactions → Legacy Cashback',
                            'legacy_user_id' => $legacyUserId,
                            'old_amount' => $data['old_membership_transactions_amount'] ?? null,
                        ],
                    ]);

                    DB::commit();
                    $summary['imported_count']++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Failed to import row for User ID {$user->id}: " . $e->getMessage());
                    $summary['failed']++;
                }
            } else {
                if (count($samples) < 3) {
                    $samples[] = [
                        'username' => $user->username,
                        'email' => $user->email,
                        'legacy_id' => $legacyUserId,
                        'amount' => $amount,
                        'current_balance' => $user->cashback_balance,
                        'expected_balance' => $user->cashback_balance + $amount
                    ];
                }
            }
        }

        fclose($handle);

        $this->info("Import Summary:");
        $this->line("- Total CSV Rows: " . $summary['total_rows']);
        $this->line("- Total Imported Users Found: " . $summary['users_found']);
        $this->line("- Users Not Found: " . $summary['users_not_found']);
        $this->line("- Users with Cashback > 0: " . $summary['with_cashback']);
        $this->line("- Users with Zero Amount (Skipped): " . $summary['zero_amount_skipped']);
        $this->line("- Users Already Imported (Skipped): " . $summary['already_imported']);
        $this->line("- Total Legacy Cashback Amount: " . number_format($summary['total_amount'], 2));
        
        if ($dryRun) {
            $this->line("- Expected Invoices to Create: " . ($summary['with_cashback'] - $summary['already_imported']));
            if (!empty($samples)) {
                $this->info("\nSample Verifications:");
                foreach ($samples as $sample) {
                    $this->line("  User: {$sample['username']} ({$sample['email']})");
                    $this->line("  - Legacy ID: {$sample['legacy_id']}");
                    $this->line("  - Cashback to Import: {$sample['amount']}");
                    $this->line("  - Current Balance: " . number_format($sample['current_balance'], 2));
                    $this->line("  - Expected Balance: " . number_format($sample['expected_balance'], 2));
                    $this->line("-----------------------------");
                }
            }
        } else {
            $this->info("\nImport Results:");
            $this->line("- Successfully Imported: " . $summary['imported_count']);
            $this->line("- Failed Rows: " . $summary['failed']);
        }

        if (!empty($unmatched)) {
            $unmatchedFile = storage_path('app/imports/legacy_membership_cashback_unmatched.csv');
            $unmatchedHandle = fopen($unmatchedFile, 'w');
            fputcsv($unmatchedHandle, ['legacy_user_id', 'email', 'customer_name', 'amount']);
            foreach ($unmatched as $u) {
                fputcsv($unmatchedHandle, $u);
            }
            fclose($unmatchedHandle);
            $this->warn("\nUnmatched users logged to: $unmatchedFile");
        }

        $this->info("\nCashback is stored in the 'membership_cashback_transactions' table.");
        $this->info("User balance is calculated dynamically via an accessor on the User model.");
        $this->info("No schema changes are required as the 'meta' field already exists.");
    }
}
