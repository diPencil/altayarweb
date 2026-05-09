<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use Illuminate\Console\Command;

class ConfirmLegacyBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:confirm-legacy {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update legacy service bookings to Confirmed status (1)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Scope to only legacy imports
        $legacyQuery = ServiceBooking::where('legacy_import', true);

        // Stats
        $totalLegacy = (clone $legacyQuery)->count();
        $currentlyPending = (clone $legacyQuery)->where('status', 0)->count();
        $currentlyApproved = (clone $legacyQuery)->where('status', 1)->count();
        $others = (clone $legacyQuery)->whereNotIn('status', [0, 1])->count();

        // Logic: Update anything that is NOT status 1 to status 1
        $toUpdateQuery = (clone $legacyQuery)->where('status', '!=', 1);
        $toUpdateCount = $toUpdateQuery->count();
        $skippedCount = $currentlyApproved;

        $this->info("---------------------------------------------");
        $this->info(" Legacy Service Bookings Update Utility ");
        $this->info("---------------------------------------------");
        if ($dryRun) {
            $this->warn(" !!! DRY RUN MODE - NO CHANGES WILL BE SAVED !!! ");
        }
        $this->line("Total legacy bookings found: $totalLegacy");
        $this->line("Bookings currently Pending:  $currentlyPending");
        $this->line("Bookings currently Approved: $currentlyApproved");
        if ($others > 0) {
            $this->line("Bookings with other status:  $others");
        }
        $this->line("---------------------------------------------");
        $this->line("Bookings that will be updated to status = 1: $toUpdateCount");
        $this->line("Skipped bookings: $skippedCount");
        $this->info("---------------------------------------------");

        if ($dryRun) {
            $this->info("Dry-run complete. Run without --dry-run to apply changes.");
            return 0;
        }

        if ($toUpdateCount === 0) {
            $this->info("No bookings need updating.");
            return 0;
        }

        if ($this->confirm("Are you sure you want to update $toUpdateCount bookings to status 1?", true)) {
            // Using update() on query builder to avoid model events/emails
            $updated = $toUpdateQuery->update(['status' => 1]);
            $this->info("Successfully updated $updated legacy bookings to status 1.");
        } else {
            $this->warn("Operation cancelled.");
        }

        return 0;
    }
}
