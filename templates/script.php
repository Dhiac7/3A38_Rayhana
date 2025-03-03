<?php
// Script de test pour l'API SightEngine
$text = 'fuck'; // Testez avec un mot inapproprié
$params = array(
    'text'       => $text,
    'lang'       => 'en',
    'categories' => 'profanity',
    'mode'       => 'rules',
    'api_user'   => '1886208732',
    'api_secret' => '6tfU9ncwxSqSwLWKMbMJkzWCPgVRSY8H'
);

$ch = curl_init('https://api.sightengine.com/1.0/text/check.json');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
$response = curl_exec($ch);
curl_close($ch);

$output = json_decode($response, true);

echo "<pre>";
print_r($output);
echo "</pre>";
