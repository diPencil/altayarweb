<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TourPackage;
use Illuminate\Support\Str;

class BackfillTourPackageSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-tour-package-slugs {--dry-run : Run the command without saving changes to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill empty or null slugs for existing tour packages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if (!\Illuminate\Support\Facades\Schema::hasColumn('tour_packages', 'slug')) {
            $this->error('The "slug" column does not exist in the "tour_packages" table. Please run migrations first.');
            return Command::FAILURE;
        }

        $packages = TourPackage::all();
        $total = $packages->count();
        $updated = 0;
        $skipped = 0;

        $this->info("Scanning $total tour packages...");
        $this->info($dryRun ? "Mode: DRY-RUN (No changes will be saved)\n" : "Mode: LIVE (Changes will be saved)\n");

        $headers = ['ID', 'Title', 'Current Slug', 'Proposed/New Slug', 'Status'];
        $rows = [];

        foreach ($packages as $pkg) {
            $currentSlug = $pkg->slug;
            
            if (empty($pkg->title)) {
                $rows[] = [$pkg->id, '(no title)', '(empty)', '(empty)', 'SKIPPED (Missing title)'];
                $skipped++;
                continue;
            }

            if (!empty($currentSlug)) {
                $rows[] = [$pkg->id, $pkg->title, $currentSlug, $currentSlug, 'SKIPPED (Already has slug)'];
                $skipped++;
                continue;
            }

            // Generate unique slug
            $baseSlug = Str::slug($pkg->title);
            $slug = $baseSlug;
            $counter = 1;
            
            while (TourPackage::where('slug', $slug)->where('id', '!=', $pkg->id)->exists()) {
                $slug = $baseSlug . '-' . $pkg->id;
                if (TourPackage::where('slug', $slug)->where('id', '!=', $pkg->id)->exists()) {
                    $slug = $baseSlug . '-' . $pkg->id . '-' . $counter;
                    $counter++;
                }
            }

            $rows[] = [$pkg->id, $pkg->title, '(empty)', $slug, $dryRun ? 'PROPOSED' : 'UPDATED'];
            $updated++;

            if (!$dryRun) {
                $pkg->slug = $slug;
                $pkg->save();
            }
        }

        $this->table($headers, $rows);

        $this->line('');
        if ($dryRun) {
            $this->info("Dry-run complete. Would update $updated records, skipped $skipped records.");
        } else {
            $this->info("Backfill complete. Successfully updated $updated records, skipped $skipped records.");
        }

        return Command::SUCCESS;
    }
}
