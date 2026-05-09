<?php

namespace App\Console\Commands;

use App\Models\Reel;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PolishReelContent extends Command
{
    protected $signature = 'reels:polish-content {--export-only : Only generate the CSV without updating database}';
    protected $description = 'Improve reel titles and descriptions with varied professional travel content';

    private $categories = [
        ['en' => 'Luxury Hotel Room Tour', 'ar' => 'جولة داخل غرفة فندقية فاخرة'],
        ['en' => 'Stunning Dubai Hotel View', 'ar' => 'إطلالة فندقية ساحرة في دبي'],
        ['en' => 'Resort Pool & Leisure Experience', 'ar' => 'تجربة مسبح ومنتجع ترفيهي'],
        ['en' => 'Premium Family Hotel Stay', 'ar' => 'إقامة فندقية عائلية مميزة'],
        ['en' => 'Breathtaking Travel Moment', 'ar' => 'لحظة سفر تخطف الأنفاس'],
        ['en' => 'Exquisite Hotel Breakfast', 'ar' => 'تجربة إفطار فندقية فاخرة'],
        ['en' => 'Room View & Balcony Moment', 'ar' => 'إطلالة من الشرفة ولحظة هدوء'],
        ['en' => 'Elite Travel Experience', 'ar' => 'تجربة سفر النخبة مع الطيار'],
        ['en' => 'Modern City View Reel', 'ar' => 'إطلالة المدينة الحديثة'],
        ['en' => 'AltayarVIP Member Stay', 'ar' => 'إقامة خاصة لأعضاء الطيار VIP'],
        ['en' => 'Hidden Travel Gems Discovery', 'ar' => 'اكتشاف جواهر السفر المخفية'],
        ['en' => 'Serene Vacation Vibes', 'ar' => 'أجواء عطلة هادئة ومريحة'],
        ['en' => 'Modern Hotel Amenities Tour', 'ar' => 'جولة في مرافق الفندق الحديثة'],
        ['en' => 'Destination Discovery Reel', 'ar' => 'فيديو اكتشاف وجهات سياحية'],
        ['en' => 'Exclusive Resort Lifestyle', 'ar' => 'نمط حياة المنتجعات الحصري'],
        ['en' => 'Premium Suite Walkthrough', 'ar' => 'جولة داخل جناح فندقي فاخر'],
        ['en' => 'Morning View & Coffee Moment', 'ar' => 'إطلالة صباحية ولحظة استرخاء'],
        ['en' => 'Top Destination Highlights', 'ar' => 'أبرز معالم الوجهات العالمية'],
    ];

    private $descriptions = [
        [
            'en' => 'Experience a world-class stay with AltayarVIP, where every detail is crafted for your comfort and luxury.',
            'ar' => 'استمتع بإقامة عالمية المستوى مع الطيار VIP، حيث تم تصميم كل التفاصيل لراحتك وفخامتك.'
        ],
        [
            'en' => 'A glimpse into the premium travel moments and high-end hotel stays available through AltayarVIP membership.',
            'ar' => 'لمحة عن لحظات السفر المميزة والإقامات الفندقية الراقية المتاحة عبر عضوية الطيار VIP.'
        ],
        [
            'en' => 'Discover stunning destinations and exceptional hotel views with our exclusive travel reels.',
            'ar' => 'اكتشف وجهات مذهلة وإطلالات فندقية استثنائية من خلال فيديوهات السفر الحصرية الخاصة بنا.'
        ],
        [
            'en' => 'Join AltayarVIP for a front-row seat to the best resorts, hotels, and travel experiences worldwide.',
            'ar' => 'انضم إلى الطيار VIP لتكون في الصفوف الأولى لأفضل المنتجعات والفنادق وتجارب السفر حول العالم.'
        ],
        [
            'en' => 'Explore the beauty of modern travel with AltayarVIP. Your journey to luxury starts here.',
            'ar' => 'استكشف جمال السفر الحديث مع الطيار VIP. رحلتك نحو الفخامة تبدأ من هنا.'
        ],
    ];

    public function handle()
    {
        $reels = Reel::orderBy('id')->get();
        $total = $reels->count();
        
        $this->info("Processing $total reels...");
        
        $csvData = [];
        $csvData[] = ['Filename', 'Current Title', 'Suggested Title EN', 'Suggested Title AR', 'Suggested Desc EN', 'Suggested Desc AR'];

        $updatedCount = 0;
        $catIndex = 0;
        $descIndex = 0;

        foreach ($reels as $index => $reel) {
            $cat = $this->categories[$catIndex % count($this->categories)];
            $desc = $this->descriptions[$descIndex % count($this->descriptions)];
            
            // Make title unique by adding a subtle index if it's a repeat category cycle
            $suffix = ($catIndex >= count($this->categories)) ? " " . (floor($catIndex / count($this->categories)) + 1) : "";
            
            $newTitleEn = $cat['en'] . $suffix;
            $newTitleAr = $cat['ar'] . $suffix;
            $newDescEn = $desc['en'];
            $newDescAr = $desc['ar'];

            $csvData[] = [
                $reel->video_path,
                $reel->title,
                $newTitleEn,
                $newTitleAr,
                $newDescEn,
                $newDescAr
            ];

            if (!$this->option('export-only')) {
                $reel->title = $newTitleEn;
                $reel->title_ar = $newTitleAr;
                $reel->description = $newDescEn;
                $reel->description_ar = $newDescAr;
                $reel->source_name = 'AltayarVIP';
                $reel->source_name_ar = 'الطيار VIP';
                $reel->save();
                $updatedCount++;
            }

            $catIndex++;
            $descIndex++;
        }

        // Export CSV
        $exportPath = base_path('../reels_content_polish_report.csv');
        $file = fopen($exportPath, 'w');
        // Add BOM for Excel UTF-8 support
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        $this->info("CSV Exported to: $exportPath");
        
        if (!$this->option('export-only')) {
            $this->info("Successfully updated $updatedCount reels in the database.");
        } else {
            $this->info("Export only mode: No database changes were made.");
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Reels Checked', $total],
                ['Titles Updated', $updatedCount],
                ['Descriptions Updated', $updatedCount],
                ['CSV Report Generated', 'Yes'],
            ]
        );

        return 0;
    }
}
