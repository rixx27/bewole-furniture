<?php

$provincesUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json';
$provincesJson = @file_get_contents($provincesUrl);
if (!$provincesJson) die("Failed to load provinces.");
$provinces = json_decode($provincesJson, true);

$data = [];

foreach ($provinces as $province) {
    $regenciesUrl = "https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$province['id']}.json";
    
    // retry logic
    $regenciesJson = false;
    for($i = 0; $i < 3; $i++) {
        $regenciesJson = @file_get_contents($regenciesUrl);
        if ($regenciesJson) break;
        sleep(1);
    }
    
    if (!$regenciesJson) continue;
    $regencies = json_decode($regenciesJson, true);
    
    if(!is_array($regencies)) continue;
    
    $cityNames = [];
    foreach ($regencies as $regency) {
        $name = ucwords(strtolower($regency['name']));
        $cityNames[] = $name;
    }
    sort($cityNames);
    
    $provinceName = ucwords(strtolower($province['name']));
    $data[$provinceName] = $cityNames;
}

ksort($data);

file_put_contents(__DIR__ . '/storage/app/indonesia_regions.json', json_encode($data, JSON_PRETTY_PRINT));

echo "Done! Total provinces: " . count($data) . "\n";
