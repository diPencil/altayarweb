<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MembershipPointTransaction;
use App\Models\MembershipCashbackTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairLegacyTransactionDates extends Command
{
    protected $signature = 'legacy:repair-transaction-dates {--dry-run}';
    protected $description = 'Repair created_at dates for legacy imported points and cashback transactions';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info("=== DRY RUN MODE ===");
        }

        $activeMembersFile = base_path('storage/app/imports/active_members_with_plans.csv');
        $cashbackFile = base_path('storage/app/imports/legacy_membership_cashback.csv');

        if (!file_exists($activeMembersFile)) {
            $this->error("Active members file not found at $activeMembersFile");
            return 1;
        }

        // Map legacy_user_id to registered_at
        $legacyDates = [];
        $handle = fopen($activeMembersFile, 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(function($h) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
        }, $headers);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) continue;
            $data = array_combine($headers, $row);
            if (isset($data['user_id'])) {
                $legacyDates[$data['user_id']] = $data['registered_at'];
            }
        }
        fclose($handle);

        // Map legacy_user_id to cashback date
        $cashbackDates = [];
        if (file_exists($cashbackFile)) {
            $handle = fopen($cashbackFile, 'r');
            $headers = fgetcsv($handle);
            $headers = array_map(function($h) {
                return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
            }, $headers);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($headers)) continue;
                $data = array_combine($headers, $row);
                $date = !empty($data['last_payment_date']) ? $data['last_payment_date'] : ($legacyDates[$data['legacy_user_id']] ?? null);
                if ($date && isset($data['legacy_user_id'])) {
                    $cashbackDates[$data['legacy_user_id']] = $date;
                }
            }
            fclose($handle);
        }

        $stats = [
            'points_found' => 0,
            'points_repaired' => 0,
            'cashback_found' => 0,
            'cashback_repaired' => 0,
            'skipped' => 0,
            'failed' => 0,
            'samples' => []
        ];

        // 1. Points Repair
        $pointTransactions = MembershipPointTransaction::where('remark', 'legacy_import')->get();
        $stats['points_found'] = $pointTransactions->count();

        foreach ($pointTransactions as $trx) {
            $user = $trx->user;
            if (!$user || !$user->legacy_user_id || !isset($legacyDates[$user->legacy_user_id])) {
                $stats['skipped']++;
                continue;
            }

            $oldDate = $trx->created_at->format('Y-m-d H:i:s');
            $newDate = date('Y-m-d H:i:s', strtotime($legacyDates[$user->legacy_user_id]));

            if ($oldDate === $newDate) {
                $stats['skipped']++;
                continue;
            }

            if (count($stats['samples']) < 5) {
                $stats['samples'][] = [
                    'user' => $user->username,
                    'type' => 'Points',
                    'before' => $oldDate,
                    'after' => $newDate
                ];
            }

            $stats['points_repaired']++;

            if (!$dryRun) {
                $trx->timestamps = false;
                $trx->created_at = $newDate;
                $trx->updated_at = $newDate;
                $trx->save();
            }
        }

        // 2. Cashback Repair
        $cashbackTransactions = MembershipCashbackTransaction::where('remark', 'legacy_membership_transactions_cashback')->get();
        $stats['cashback_found'] = $cashbackTransactions->count();

        foreach ($cashbackTransactions as $trx) {
            $user = $trx->user;
            if (!$user || !$user->legacy_user_id || !isset($cashbackDates[$user->legacy_user_id])) {
                $stats['skipped']++;
                continue;
            }

            $oldDate = $trx->created_at->format('Y-m-d H:i:s');
            $newDate = date('Y-m-d H:i:s', strtotime($cashbackDates[$user->legacy_user_id]));

            if ($oldDate === $newDate) {
                $stats['skipped']++;
                continue;
            }

            if (count($stats['samples']) < 10) {
                $stats['samples'][] = [
                    'user' => $user->username,
                    'type' => 'Cashback',
                    'before' => $oldDate,
                    'after' => $newDate
                ];
            }

            $stats['cashback_repaired']++;

            if (!$dryRun) {
                $trx->timestamps = false;
                $trx->created_at = $newDate;
                $trx->updated_at = $newDate;
                $trx->save();
            }
        }

        $this->info("\n--- Repair Summary ---");
        $this->line("Legacy Point Transactions Found:    " . $stats['points_found']);
        $this->line("Point Transactions to Repair:       " . $stats['points_repaired']);
        $this->line("Legacy Cashback Transactions Found: " . $stats['cashback_found']);
        $this->line("Cashback Transactions to Repair:    " . $stats['cashback_repaired']);
        $this->line("Skipped Rows:                       " . $stats['skipped']);
        $this->line("Failed Rows:                        " . $stats['failed']);

        if (!empty($stats['samples'])) {
            $this->info("\n--- Samples (Before/After) ---");
            foreach ($stats['samples'] as $sample) {
                $this->line("User: {$sample['user']} ({$sample['type']}) | Before: {$sample['before']} | After: {$sample['after']}");
            }
        }

        if ($dryRun) {
            $this->warn("\nDry-run complete. No changes were made.");
        } else {
            $this->info("\nRepair complete.");
        }

        return 0;
    }
}
