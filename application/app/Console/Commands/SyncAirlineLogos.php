<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncAirlineLogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:airline-logos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync airline logo files from assets/images/airlines to public/assets/images/airlines';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $source = base_path('assets/images/airlines');
        $dest = public_path('assets/images/airlines');

        if (!File::exists($source) || !File::isDirectory($source)) {
            $this->info("Source directory not found: {$source}");
            return 0;
        }

        if (!File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true);
        }

        $files = File::files($source);

        foreach ($files as $file) {
            $src = $file->getPathname();
            $destPath = $dest . DIRECTORY_SEPARATOR . $file->getFilename();

            try {
                if (!File::exists($destPath) || File::lastModified($src) > File::lastModified($destPath)) {
                    File::copy($src, $destPath);
                    @chmod($destPath, 0644);
                    $this->info('Copied: ' . $file->getFilename());
                }
            } catch (\Exception $e) {
                $this->error('Failed copying ' . $file->getFilename() . ': ' . $e->getMessage());
            }
        }

        $this->info('Airline logos sync finished.');
        return 0;
    }
}
