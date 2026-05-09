<?php

namespace App\Console\Commands;

use App\Models\Reel;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ImportReels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reels:import-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk import reel videos from assets/videos/reels into the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Reels Import...');

        // Define the path to the reels videos
        // Path in root: assets/videos/reels
        // Laravel base_path: application/
        $reelsPath = base_path('../assets/videos/reels');

        if (!File::isDirectory($reelsPath)) {
            $this->error("Directory not found: $reelsPath");
            return 1;
        }

        $files = File::files($reelsPath);
        $extensions = ['mp4', 'mov', 'webm', 'm4v'];
        
        $totalFiles = count($files);
        $count = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;

        $this->info("Found $totalFiles files in $reelsPath");

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $extension = strtolower($file->getExtension());

            if (!in_array($extension, $extensions)) {
                $this->line("Skipping non-video file: $filename", 'comment');
                $skipped++;
                continue;
            }

            try {
                $count++;

                // Clean title from filename
                $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                $cleanTitle = $this->cleanTitle($nameWithoutExt);

                $reel = Reel::where('video_path', $filename)->first();

                if ($reel) {
                    // Update existing
                    $reel->title = $cleanTitle;
                    $reel->title_ar = 'ريل سياحي من الطيار VIP';
                    $reel->description = 'A short travel reel from AltayarVIP showcasing hotels, destinations, and travel moments.';
                    $reel->description_ar = 'فيديو قصير يعرض لحظات سفر وفنادق ووجهات مميزة من الطيار VIP.';
                    $reel->status = 1; // Active
                    // Keep existing sort_order if set, otherwise assign
                    if (!$reel->sort_order) {
                        $reel->sort_order = $count * 10;
                    }
                    $reel->save();
                    $updated++;
                    $this->line("Updated: $filename", 'info');
                } else {
                    // Create new
                    $reel = new Reel();
                    $reel->uploaded_by = 1; // Default admin
                    $reel->title = $cleanTitle;
                    $reel->title_ar = 'ريل سياحي من الطيار VIP';
                    $reel->description = 'A short travel reel from AltayarVIP showcasing hotels, destinations, and travel moments.';
                    $reel->description_ar = 'فيديو قصير يعرض لحظات سفر وفنادق ووجهات مميزة من الطيار VIP.';
                    $reel->video_path = $filename;
                    $reel->status = 1; // Active
                    $reel->sort_order = $count * 10;
                    $reel->views_count = 0;
                    $reel->likes_count = 0;
                    $reel->saves_count = 0;
                    $reel->save();
                    $created++;
                    $this->line("Created: $filename", 'info');
                }
            } catch (\Exception $e) {
                $this->error("Failed to import $filename: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Import Process Finished.");
        $this->table(
            ['Category', 'Count'],
            [
                ['Total Files Found', $totalFiles],
                ['Videos Processed', $count],
                ['New Reels Created', $created],
                ['Existing Reels Updated', $updated],
                ['Files Skipped', $skipped],
                ['Imports Failed', $failed],
            ]
        );

        $this->info("Public URL Pattern: /assets/videos/reels/{filename}");
        
        return 0;
    }

    /**
     * Clean filename to human-readable title
     */
    private function cleanTitle($filename)
    {
        // Remove common separators and WhatsApp-style extra info
        $clean = str_replace(['-', '_', '.', 'at'], ' ', $filename);
        
        // Remove multiple spaces
        $clean = preg_replace('/\s+/', ' ', $clean);
        
        // Title Case
        $clean = Str::title(trim($clean));

        // If it's too long or messy (e.g. random string or WhatsApp format), provide a generic fallback
        if (strlen($clean) > 50 || preg_match('/[0-9]{5,}/', $clean)) {
             // Try to extract date pattern if present (YYYY-MM-DD or similar)
             if (preg_match('/([0-9]{4})\s([0-9]{2})\s([0-9]{2})/', $clean, $matches)) {
                 return "Travel Moment " . $matches[1] . "-" . $matches[2] . "-" . $matches[3];
             }
             return "AltayarVIP Travel Reel " . date('Y-m-d');
        }

        return $clean ?: "Travel Reel";
    }
}
