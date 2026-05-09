<?php
$file = 'd:\\Development\\altayar\\altayarbookingvp\\application\\resources\\lang\\ar.json';
$content = file_get_contents($file);

// If the content is UTF-8 but contains characters like Ø§Ù„ØªØ­Ù‚Ù‚
// we need to convert those characters back to bytes and then to UTF-8
$data = json_decode($content, true);

function fixMojibake($string) {
    if (empty($string)) return $string;
    // Try to convert from UTF-8 to ISO-8859-1 (Latin1) to get the raw bytes
    // then treat those bytes as UTF-8
    $latin1 = @mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
    $utf8 = @mb_convert_encoding($latin1, 'UTF-8', 'ISO-8859-1');
    
    // If it looks like valid Arabic now, return it
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $utf8)) {
        return $utf8;
    }
    
    // Fallback: sometimes it's just raw bytes that need to be treated as UTF-8
    // This is tricky in PHP without a byte array, but let's try iconv
    $fixed = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $string);
    if ($fixed && preg_match('/[\x{0600}-\x{06FF}]/u', $fixed)) {
        return $fixed;
    }

    return $string;
}

if ($data) {
    $fixedData = [];
    foreach($data as $key => $value) {
        $fixedData[$key] = fixMojibake($value);
    }
    file_put_contents($file, json_encode($fixedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Attempted to fix mojibake in " . count($fixedData) . " keys.\n";
} else {
    echo "Could not decode JSON.\n";
}
