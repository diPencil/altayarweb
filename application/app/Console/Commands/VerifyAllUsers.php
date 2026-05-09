<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify-all {--dry-run : Whether to run in dry-run mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable 2FA Verification and KYC for imported users only';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? "Starting Dry-Run Verification Update (Scoped to Imported Users)..." : "Starting Real Verification Update (Scoped to Imported Users)...");

        $totalUsers = User::count();
        $importedUsersQuery = User::whereNotNull('legacy_user_id');
        
        $totalImported = (clone $importedUsersQuery)->count();
        $needTvUpdate = (clone $importedUsersQuery)->where('tv', 0)->count();
        $needKvUpdate = (clone $importedUsersQuery)->where('kv', '!=', 1)->count();
        $alreadyVerified = (clone $importedUsersQuery)->where('tv', 1)->where('kv', 1)->count();
        $skippedNonImported = $totalUsers - $totalImported;

        $this->line("Total Users in DB:       {$totalUsers}");
        $this->line("Total Imported Users:    {$totalImported}");
        $this->line("Users needing 2FA (tv):  {$needTvUpdate}");
        $this->line("Users needing KYC (kv):  {$needKvUpdate}");
        $this->line("Users already verified:  {$alreadyVerified}");
        $this->line("Skipped Non-Imported:    {$skippedNonImported}");
        
        $this->info("\nCONFIRMATION: This update will ONLY affect users where legacy_user_id is not null.");
        $this->info("Admin, Staff, and System accounts (legacy_user_id is null) will NOT be modified.");

        if ($dryRun) {
            $this->warn("\nDry-run complete. No changes were made.");
            return 0;
        }

        if (!$this->confirm('Do you really want to verify all imported users?')) {
            $this->warn('Action cancelled.');
            return 0;
        }

        $updatedCount = User::whereNotNull('legacy_user_id')
            ->where(function($q) {
                $q->where('tv', 0)->orWhere('kv', '!=', 1)->orWhere('ev', 0)->orWhere('sv', 0)->orWhere('status', 0);
            })->update([
                'tv' => 1,
                'kv' => 1,
                'ev' => 1,
                'sv' => 1,
                'status' => 1,
            ]);

        $this->info("\nSuccessfully updated {$updatedCount} imported users.");
        return 0;
    }
}
