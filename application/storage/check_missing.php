<?php
$arFile = 'd:\\Development\\altayar\\altayarbookingvp\\application\\resources\\lang\\ar.json';
$keysFile = 'd:\\Development\\altayar\\altayarbookingvp\\application\\storage\\all_translation_keys.txt';

$arData = json_decode(file_get_contents($arFile), true);
$allKeys = file($keysFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$missingKeys = [];
foreach($allKeys as $key) {
    if (!isset($arData[$key]) || empty($arData[$key])) {
        $missingKeys[] = $key;
    }
}

file_put_contents('d:\\Development\\altayar\\altayarbookingvp\\application\\storage\\missing_translations.txt', implode("\n", $missingKeys));
echo "Found " . count($missingKeys) . " missing or empty translations.\n";
