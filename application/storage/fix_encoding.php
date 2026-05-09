<?php
$file = 'd:\\Development\\altayar\\altayarbookingvp\\application\\resources\\lang\\ar.json';
$content = file_get_contents($file);

// Try to decode. If it fails, it might be already corrupted or double encoded
$data = json_decode($content, true);

if (!$data) {
    echo "JSON decode failed. Attempting to fix common mojibake...\n";
    // This is a last resort - if the file is saved as UTF-8 but contains Latin1 interpretations of UTF-8
    $fixedContent = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
    $data = json_decode($fixedContent, true);
}

if ($data) {
    ksort($data);
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Successfully fixed ar.json encoding and sorted keys.\n";
} else {
    echo "Critical: Could not recover ar.json. Please check file content.\n";
    // Let's print a small sample to see what we are dealing with
    echo "Sample: " . substr($content, 0, 500) . "\n";
}
