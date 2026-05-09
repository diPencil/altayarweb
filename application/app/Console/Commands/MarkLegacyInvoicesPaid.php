<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkLegacyInvoicesPaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:mark-legacy-paid {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update legacy invoices to Paid status (1)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Scope to only legacy imports
        $legacyQuery = Invoice::where('legacy_import', true);

        // Stats
        $totalLegacy = (clone $legacyQuery)->count();
        $currentlyPending = (clone $legacyQuery)->where('status', 0)->count();
        $currentlyPaid = (clone $legacyQuery)->where('status', 1)->count();
        $currentlyPartial = (clone $legacyQuery)->where('status', 2)->count();
        $currentlyCancelled = (clone $legacyQuery)->where('status', 3)->count();

        // Logic: Update anything that is NOT status 1 to status 1
        $toUpdateQuery = (clone $legacyQuery)->where('status', '!=', 1);
        $toUpdateCount = $toUpdateQuery->count();
        $skippedCount = $currentlyPaid;

        $this->info("---------------------------------------------");
        $this->info(" Legacy Invoices Update Utility ");
        $this->info("---------------------------------------------");
        if ($dryRun) {
            $this->warn(" !!! DRY RUN MODE - NO CHANGES WILL BE SAVED !!! ");
        }
        $this->line("Total legacy invoices found: $totalLegacy");
        $this->line("Invoices currently Pending:   $currentlyPending");
        $this->line("Invoices currently Paid:      $currentlyPaid");
        $this->line("Invoices currently Partial:   $currentlyPartial");
        $this->line("Invoices currently Cancelled: $currentlyCancelled");
        $this->line("---------------------------------------------");
        $this->line("Invoices that will be updated to status = 1: $toUpdateCount");
        $this->line("Skipped invoices: $skippedCount");
        $this->info("---------------------------------------------");

        if ($dryRun) {
            $this->info("Dry-run complete. Run without --dry-run to apply changes.");
            return 0;
        }

        if ($toUpdateCount === 0) {
            $this->info("No invoices need updating.");
            return 0;
        }

        if ($this->confirm("Are you sure you want to update $toUpdateCount invoices to status 1?", true)) {
            // Using update() on query builder to avoid model events/emails/transactions
            $updated = $toUpdateQuery->update(['status' => 1]);
            $this->info("Successfully updated $updated legacy invoices to status 1.");
        } else {
            $this->warn("Operation cancelled.");
        }

        return 0;
    }
}
