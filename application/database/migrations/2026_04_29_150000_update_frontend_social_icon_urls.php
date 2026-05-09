<?php

use App\Models\Frontend;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const SOCIAL_URLS = [
        'facebook' => 'https://www.facebook.com/altayarvipcom',
        'instagram' => 'https://www.instagram.com/altayarvip/',
        'x' => 'https://x.com/altayarvipcom',
        'linkedin' => 'https://www.linkedin.com/company/altayarvip',
        'snapchat' => 'https://www.snapchat.com/@altayarvip',
    ];

    public function up(): void
    {
        $snapchatSeen = false;

        $rows = Frontend::query()->where('data_keys', 'social_icon.element')->get();

        foreach ($rows as $row) {
            $values = (array) $row->data_values;
            $titleKey = strtolower(trim((string) ($values['title'] ?? '')));

            $urlKey = match ($titleKey) {
                'facebook' => 'facebook',
                'instagram' => 'instagram',
                'x', 'twitter' => 'x',
                'linkedin' => 'linkedin',
                'snapchat' => 'snapchat',
                default => null,
            };

            if ($urlKey === 'snapchat') {
                $snapchatSeen = true;
            }

            if ($urlKey !== null && isset(self::SOCIAL_URLS[$urlKey])) {
                $values['url'] = self::SOCIAL_URLS[$urlKey];
                $row->data_values = $values;
                $row->save();
            }
        }

        if (! $snapchatSeen) {
            $snap = new Frontend();
            $snap->data_keys = 'social_icon.element';
            $snap->data_values = [
                'title' => 'Snapchat',
                'social_icon' => '<i class="fab fa-snapchat-ghost"></i>',
                'url' => self::SOCIAL_URLS['snapchat'],
            ];
            $snap->save();
        }
    }

    public function down(): void
    {
        //
    }
};
