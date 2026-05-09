<?php

namespace App\Traits;

trait FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This trait basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo(){
        $data['withdrawVerify'] = [
            'path'=>'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path'      =>'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      =>'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/general/default.png',
        ];
        $data['withdrawMethod'] = [
            'path'      => 'assets/images/withdraw/method',
            'size'      => '800x800',
        ];
        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path'      => 'assets/images/general',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/plugins',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      =>'assets/images/user/profile',
            'size'      =>'350x300',
        ];
        $data['employeeProfile'] = [
            'path'      =>'assets/images/user/profile',
            'size'      =>'350x300',
        ];
        $data['adminProfile'] = [
            'path'      =>'assets/admin/images/profile',
            'size'      =>'400x400',
        ];
        $data['category'] = [
            'path'      =>'assets/images/frontend/category',
            'size'      =>'50x50',
        ];
  
        $data['coverImage'] = [
            'path'      =>'assets/images/frontend/coverImage',
            'size'      =>'1296x368',
        ];
        
        $data['frontend'] = [
            'path'   =>'assets/images/frontend',
        ];

        $data['bannerImage'] = [
            'path'   =>'assets/images/frontend/banner',
        ];

        $data['whyChooseUs'] = [
            'path'   =>'assets/images/frontend/why_choose_us',
        ];

        $data['testimonial'] = [
            'path'   =>'assets/images/frontend/testimonial',
        ];

        $data['ourBestOffer'] = [
            'path'   =>'assets/images/frontend/our_best_offer',
        ];

        $data['popupAd'] = [
            'path'   =>'assets/images/popup_ads',
        ];

        $data['aboutMe'] = [
            'path'   =>'assets/images/frontend/about_me',
        ];

        $data['aboutOffer'] = [
            'path'   =>'assets/images/frontend/about_offer',
        ];

        $data['aboutEmployee'] = [
            'path'   =>'assets/images/frontend/about_agent',
        ];

        $data['location'] = [
            'path'      =>'assets/images/frontend/location',
            'size'      =>'1024x680',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '50x50'
        ];

        $data['tourPackageImage'] = [
            'path'   =>'assets/images/tour_package_image',
            'size'      =>'855x535',
        ];
        $data['listingImage'] = [
            'path'   =>'assets/images/listing_image',
        ];
        $data['reelVideo'] = [
            'path'      => 'assets/videos/reels',
        ];
        $data['reelThumbnail'] = [
            'path'      => 'assets/images/reels',
            'size'      => '720x1280',
        ];
        $data['membershipPlanPdf'] = [
            'path'      =>'assets/images/membership/plan-pdf',
        ];
        $data['membershipPlanImage'] = [
            'path'      =>'assets/images/membership/plan-image',
        ];
        $data['membershipPlanCover'] = [
            'path'      =>'assets/images/membership/plan-cover',
        ];
        $data['privilegeCardImage'] = [
            'path'      => 'assets/images/privilege-card/image',
        ];
        $data['privilegeCardPdf'] = [
            'path'      => 'assets/images/privilege-card/pdf',
        ];
        return $data;
	}

}
