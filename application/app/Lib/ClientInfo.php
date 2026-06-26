<?php

namespace App\Lib;

class ClientInfo{

    /**
    * Get requestor IP information
    *
    * @return array
    */
	public static function ipInfo()
	{
	    $ip = getRealIP();

	    $xml = @simplexml_load_file("https://ssl.geoplugin.net/xml.gp?ip=" . $ip);

	    $country = $xml ? ($xml->geoplugin_countryName ?? 'Unknown') : 'Unknown';
	    $city = $xml ? ($xml->geoplugin_city ?? 'Unknown') : 'Unknown';
	    $area = $xml ? ($xml->geoplugin_areaCode ?? 'Unknown') : 'Unknown';
	    $code = $xml ? ($xml->geoplugin_countryCode ?? 'Unknown') : 'Unknown';
	    $long = $xml ? ($xml->geoplugin_longitude ?? 'Unknown') : 'Unknown';
	    $lat = $xml ? ($xml->geoplugin_latitude ?? 'Unknown') : 'Unknown';

	    $data['country'] = (string) $country;
	    $data['city'] = (string) $city;
	    $data['area'] = (string) $area;
	    $data['code'] = (string) $code;
	    $data['long'] = (string) $long;
	    $data['lat'] = (string) $lat;
	    $data['ip'] = $ip;
	    $data['time'] = date('Y-m-d h:i:s A');

	    return $data;
	}

    /**
    * Get requestor operating system information
    *
    * @return array
    */
	public static function osBrowser(){
	    $userAgent = $_SERVER['HTTP_USER_AGENT'];
	    $osPlatform = "Unknown OS Platform";
	    $osArray = array(
	        '/windows nt 10/i' => 'Windows 10',
	        '/windows nt 6.3/i' => 'Windows 8.1',
	        '/windows nt 6.2/i' => 'Windows 8',
	        '/windows nt 6.1/i' => 'Windows 7',
	        '/windows nt 6.0/i' => 'Windows Vista',
	        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
	        '/windows nt 5.1/i' => 'Windows XP',
	        '/windows xp/i' => 'Windows XP',
	        '/windows nt 5.0/i' => 'Windows 2000',
	        '/windows me/i' => 'Windows ME',
	        '/win98/i' => 'Windows 98',
	        '/win95/i' => 'Windows 95',
	        '/win16/i' => 'Windows 3.11',
	        '/macintosh|mac os x/i' => 'Mac OS X',
	        '/mac_powerpc/i' => 'Mac OS 9',
	        '/linux/i' => 'Linux',
	        '/ubuntu/i' => 'Ubuntu',
	        '/iphone/i' => 'iPhone',
	        '/ipod/i' => 'iPod',
	        '/ipad/i' => 'iPad',
	        '/android/i' => 'Android',
	        '/blackberry/i' => 'BlackBerry',
	        '/webos/i' => 'Mobile'
	    );
	    foreach ($osArray as $regex => $value) {
	        if (preg_match($regex, $userAgent)) {
	            $osPlatform = $value;
	        }
	    }
	    $browser = "Unknown Browser";
	    $browserArray = array(
	        '/msie/i' => 'Internet Explorer',
	        '/firefox/i' => 'Firefox',
	        '/safari/i' => 'Safari',
	        '/chrome/i' => 'Chrome',
	        '/edge/i' => 'Edge',
	        '/opera/i' => 'Opera',
	        '/netscape/i' => 'Netscape',
	        '/maxthon/i' => 'Maxthon',
	        '/konqueror/i' => 'Konqueror',
	        '/mobile/i' => 'Handheld Browser'
	    );
	    foreach ($browserArray as $regex => $value) {
	        if (preg_match($regex, $userAgent)) {
	            $browser = $value;
	        }
	    }

	    $data['os_platform'] = $osPlatform;
	    $data['browser'] = $browser;

	    return $data;
	}

}
