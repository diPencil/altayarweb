<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Mobile Onboarding Settings
     */
    public function onboarding(): JsonResponse
    {
        return response()->json([
            'onboarding' => [
                'image' => 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=1974&auto=format&fit=crop',
                'title' => [
                    'en' => 'Explore your journey only with us',
                    'ar' => 'استكشف رحلتك معنا فقط',
                ],
                'subtitle' => [
                    'en' => 'All your vacations destinations are here, enjoy your holiday',
                    'ar' => 'جميع وجهات عطلاتك هنا، استمتع بعطلتك',
                ],
            ]
        ]);
    }

    /**
     * Mobile Support Settings
     */
    public function support(): JsonResponse
    {
        $contact = DB::table('frontends')->where('data_keys', 'contact_us.content')->first();
        $contactData = $contact ? json_decode($contact->data_values, true) : [];

        $rawPhone = $contactData['contact_number_en'] ?? '+966 57 473 4062';
        $whatsapp = preg_replace('/[^0-9]/', '', $rawPhone); // Extract only digits for WhatsApp link

        return response()->json([
            'contact_us' => [
                'whatsapp_number' => $whatsapp,
                'call_number' => $rawPhone,
                'email' => $contactData['email_address_en'] ?? 'info@altayarvip.com',
            ]
        ]);
    }

    /**
     * Mobile About Links
     */
    public function about(): JsonResponse
    {
        $socials = DB::table('frontends')->where('data_keys', 'social_icon.element')->get();
        $socialLinks = [];
        foreach ($socials as $social) {
            $data = json_decode($social->data_values, true);
            $title = strtolower($data['title'] ?? '');
            if (in_array($title, ['facebook', 'instagram', 'x', 'twitter', 'linkedin', 'snapchat', 'tiktok'])) {
                $key = $title === 'twitter' ? 'x' : $title;
                $socialLinks[$key] = $data['url'] ?? '';
            }
        }

        return response()->json([
            'about_links' => [
                'websites' => [
                    'official' => 'https://altayarvip.com',
                    'hotel_booking' => 'https://altayarvip.com',
                ],
                'socials' => array_merge([
                    'facebook' => '',
                    'instagram' => '',
                    'x' => '',
                    'linkedin' => '',
                    'snapchat' => '',
                    'tiktok' => '',
                ], $socialLinks),
            ]
        ]);
    }

    /**
     * General Unified Settings for Mobile
     */
    public function general(): JsonResponse
    {
        $gs = GeneralSetting::first();
        $contact = DB::table('frontends')->where('data_keys', 'contact_us.content')->first();
        $contactData = $contact ? json_decode($contact->data_values, true) : [];

        $privacy = DB::table('frontends')->where('data_keys', 'policy_pages.element')->where('data_values', 'LIKE', '%Privacy Policy%')->first();
        $terms = DB::table('frontends')->where('data_keys', 'policy_pages.element')->where('data_values', 'LIKE', '%Terms of Service%')->first();
        
        $privacyId = $privacy ? $privacy->id : 1;
        $termsId = $terms ? $terms->id : 2;

        return response()->json([
            'app_name' => $gs->site_name ?? 'Altayar VIP',
            'company_name' => $gs->site_name ?? 'Altayar VIP',
            'logo' => asset('assets/images/logoIcon/logo.png'),
            'favicon' => asset('assets/images/logoIcon/favicon.png'),
            'dark_logo' => asset('assets/images/logoIcon/logo_dark.png'),
            'default_language' => 'en',
            'supported_languages' => ['en', 'ar'],
            'default_currency' => $gs->cur_text ?? 'USD',
            'supported_currencies' => [$gs->cur_text ?? 'USD'],
            'support_phone' => $contactData['contact_number_en'] ?? '+966 57 473 4062',
            'support_email' => $contactData['email_address_en'] ?? 'info@altayarvip.com',
            'whatsapp_number' => preg_replace('/[^0-9]/', '', $contactData['contact_number_en'] ?? '+966 57 473 4062'),
            'terms_url' => url('/policy/' . $termsId . '/terms-of-service'),
            'privacy_url' => url('/policy/' . $privacyId . '/privacy-policy'),
            'about_short_description' => $contactData['short_details_en'] ?? '',
            'feature_flags' => [
                'enable_vouchers' => false,
                'enable_qr_membership' => false,
                'enable_reels' => true,
            ]
        ]);
    }
}
