<?php

namespace App\Console\Commands;

use App\Models\MembershipPlan;
use App\Models\MembershipPointTransaction;
use App\Models\User;
use App\Models\UserMembership;
use App\Models\MembershipPlanHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportActiveMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:import-active 
                            {--file=storage/app/imports/active_members_with_plans.csv : The path to the CSV file} 
                            {--dry-run : Whether to run in dry-run mode}
                            {--repair-imported-dates : Repair created_at dates for already imported users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import active members from WordPress CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file');
        $dryRun = $this->option('dry-run');
        $repairMode = $this->option('repair-imported-dates');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            $this->warn("Please ensure the file exists at: " . base_path($file));
            return 1;
        }

        if ($repairMode) {
            return $this->repairImportedDates($file, $dryRun);
        }

        $this->info($dryRun ? "Starting Dry-Run Import..." : "Starting Real Import...");

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        // Clean headers: remove BOM and trim
        $headers = array_map(function($header) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header));
        }, $headers);
        
        $stats = [
            'total_rows' => 0,
            'valid_active' => 0,
            'to_create' => 0,
            'to_update' => 0,
            'skipped' => 0,
            'failed' => 0,
            'duplicates_in_csv' => 0,
            'plans' => [],
            'unknown_plans' => [],
            'total_points' => 0,
            'total_payments' => 0,
        ];

        $emailsInCsv = [];
        $plans = MembershipPlan::all();
        $planMapByName = $plans->pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        
        $planIdFallback = [
            1 => 'Business Membership',
            2 => 'Diamond Membership',
            3 => 'VIP Membership',
            4 => 'Platinum Membership',
            5 => 'Gold Membership',
            6 => 'Silver Membership',
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $stats['total_rows']++;
            $data = array_combine($headers, $row);

            $email = trim($data['email']);
            $status = trim($data['membership_status']);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $stats['failed']++;
                continue;
            }

            if (isset($emailsInCsv[$email])) {
                $stats['duplicates_in_csv']++;
                $stats['skipped']++;
                continue;
            }
            $emailsInCsv[$email] = true;

            if ($status !== 'Active') {
                $stats['skipped']++;
                continue;
            }

            $stats['valid_active']++;

            $targetPlanId = null;
            $csvPlanId = (int)$data['plan_id'];
            $csvPlanName = trim($data['plan_name']);

            if (isset($planIdFallback[$csvPlanId])) {
                $mappedName = $planIdFallback[$csvPlanId];
                if (isset($planMapByName[strtolower($mappedName)])) {
                    $targetPlanId = $planMapByName[strtolower($mappedName)];
                }
            }

            if (!$targetPlanId && isset($planMapByName[strtolower($csvPlanName)])) {
                $targetPlanId = $planMapByName[strtolower($csvPlanName)];
            }

            if (!$targetPlanId) {
                $stats['failed']++;
                continue;
            }

            $stats['plans'][$csvPlanName] = ($stats['plans'][$csvPlanName] ?? 0) + 1;
            $stats['total_points'] += (int)$data['current_points'];
            $stats['total_payments'] += (float)$data['total_membership_paid'];

            $user = User::where('email', $email)->first();
            if ($user) {
                $stats['to_update']++;
            } else {
                $stats['to_create']++;
            }

            if (!$dryRun) {
                try {
                    DB::beginTransaction();

                    if (!$user) {
                        $user = new User();
                        $user->email = $email;
                        $user->password = bcrypt(Str::random(16));
                        $user->status = 1;
                        $user->ev = 1;
                        $user->sv = 1;
                        $user->kv = 0;
                        // Set historical registration date
                        if (!empty($data['registered_at'])) {
                            $user->created_at = date('Y-m-d H:i:s', strtotime($data['registered_at']));
                        }
                    }

                    if (!empty($data['username'])) $user->username = trim($data['username']);
                    if (!empty($data['first_name'])) $user->firstname = trim($data['first_name']);
                    if (!empty($data['last_name'])) $user->lastname = trim($data['last_name']);
                    if (!empty($data['phone'])) $user->mobile = trim($data['phone']);
                    
                    $user->legacy_user_id = (int)$data['user_id'];
                    $user->save();

                    $pointsToImport = (int)$data['current_points'];
                    $hasPointsImported = MembershipPointTransaction::where('user_id', $user->id)
                        ->where('remark', 'legacy_import')
                        ->exists();

                    if (!$hasPointsImported && $pointsToImport > 0) {
                        MembershipPointTransaction::create([
                            'user_id' => $user->id,
                            'trx' => getTrx(),
                            'type' => 'earned',
                            'points' => $pointsToImport,
                            'balance_after' => (int)$user->membership_points_balance + $pointsToImport,
                            'remark' => 'legacy_import',
                            'meta' => [
                                'old_user_id' => $data['user_id'],
                                'bonus_points_preserved' => (int)$data['bonus_points'],
                            ]
                        ]);
                    }

                    $currentMembership = $user->currentMembership;
                    $needsNewMembership = true;

                    if ($currentMembership) {
                        if ($currentMembership->membership_plan_id == $targetPlanId) {
                            $needsNewMembership = false;
                        } else {
                            $currentMembership->status = 2;
                            $currentMembership->save();
                        }
                    }

                    if ($needsNewMembership) {
                        $plan = MembershipPlan::find($targetPlanId);
                        
                        $membership = new UserMembership();
                        $membership->user_id = $user->id;
                        $membership->membership_plan_id = $targetPlanId;
                        $membership->start_date = $data['registered_at'] ? date('Y-m-d', strtotime($data['registered_at'])) : now()->toDateString();
                        $membership->status = 1;
                        $membership->payment_summary = [
                            'total_paid' => $data['total_membership_paid'],
                            'payment_count' => $data['payment_count'],
                            'last_payment_amount' => $data['last_payment_amount'],
                            'last_payment_date' => $data['last_payment_date'],
                            'last_payment_gateway' => $data['last_payment_gateway'],
                            'last_payment_status' => $data['last_payment_status'],
                            'legacy_import' => true
                        ];
                        $membership->save();

                        MembershipPlanHistory::recordChange($user, $currentMembership, $membership, $plan, [
                            'note' => 'legacy_import',
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $stats['failed']++;
                    Log::error("Import error for {$email}: " . $e->getMessage());
                }
            }
        }

        fclose($handle);

        $this->info("\n--- Import Summary ---");
        $this->line("Total CSV Rows:        {$stats['total_rows']}");
        $this->line("Valid Active Rows:     {$stats['valid_active']}");
        $this->line("Users to Create:       {$stats['to_create']}");
        $this->line("Users to Update:       {$stats['to_update']}");
        $this->line("Skipped Rows:          {$stats['skipped']}");
        $this->line("Failed Rows:           {$stats['failed']}");
        
        if ($dryRun) {
            $this->warn("\nDry-run complete. No changes were made.");
        } else {
            $this->info("\nImport complete.");
        }
    }

    /**
     * Repair created_at dates for already imported users.
     */
    protected function repairImportedDates($file, $dryRun)
    {
        $this->info($dryRun ? "Starting Dry-Run Repair..." : "Starting Real Repair...");

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(function($header) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header));
        }, $headers);

        $stats = [
            'users_found' => 0,
            'repaired' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            $email = trim($data['email']);
            $legacyUserId = (int)$data['user_id'];
            $registeredAt = !empty($data['registered_at']) ? date('Y-m-d H:i:s', strtotime($data['registered_at'])) : null;

            if (!$registeredAt) {
                $stats['skipped']++;
                continue;
            }

            // Find user by legacy_user_id or email
            $user = User::where('legacy_user_id', $legacyUserId)
                        ->orWhere('email', $email)
                        ->first();

            if (!$user || $user->legacy_user_id != $legacyUserId) {
                $stats['skipped']++;
                continue;
            }

            $stats['users_found']++;

            if ($user->created_at->format('Y-m-d H:i:s') === $registeredAt) {
                $stats['skipped']++;
                continue;
            }

            $stats['repaired']++;

            if (!$dryRun) {
                try {
                    // Update only the created_at column to avoid triggering other logic
                    $user->timestamps = false;
                    $user->created_at = $registeredAt;
                    $user->save();
                    $user->timestamps = true;
                } catch (\Exception $e) {
                    $stats['failed']++;
                    $this->error("Failed to repair {$email}: " . $e->getMessage());
                }
            }
        }

        fclose($handle);

        $this->info("\n--- Repair Summary ---");
        $this->line("Users Found:           {$stats['users_found']}");
        $this->line("Repaired:              {$stats['repaired']}");
        $this->line("Skipped/Already OK:    {$stats['skipped']}");
        $this->line("Failed:                {$stats['failed']}");

        if ($dryRun) {
            $this->warn("\nDry-run complete. No changes were made.");
        } else {
            $this->info("\nRepair complete.");
        }
        
        return 0;
    }
}
