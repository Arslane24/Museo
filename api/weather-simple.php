<?php
/**
 * API Météo Simple - Appel direct à OpenWeather
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Récupération de la ville
$city = $_GET['city'] ?? 'Paris';

// Clé API OpenWeather
$apiKey = '09bb84206c31c4428d3df828199144fb';

// Construction de l'URL
$url = "https://api.openweathermap.org/data/2.5/weather?" . http_build_query([
    'q' => $city,
    'appid' => $apiKey,
    'units' => 'metric',
    'lang' => 'fr'
]);

// Appel à l'API avec curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Traitement de la réponse
if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    
    // Formater les données météo
    echo json_encode([
        'success' => true,
        'weather' => [
            'city' => $data['name'] ?? $city,
            'country' => $data['sys']['country'] ?? '',
            'temperature' => round($data['main']['temp'] ?? 0),
            'feels_like' => round($data['main']['feels_like'] ?? 0),
            'temp_min' => round($data['main']['temp_min'] ?? 0),
            'temp_max' => round($data['main']['temp_max'] ?? 0),
            'description' => ucfirst($data['weather'][0]['description'] ?? ''),
            'icon' => $data['weather'][0]['icon'] ?? '',
            'icon_url' => "https://openweathermap.org/img/wn/" . ($data['weather'][0]['icon'] ?? '') . "@2x.png",
            'humidity' => $data['main']['humidity'] ?? 0,
            'pressure' => $data['main']['pressure'] ?? 0,
            'wind_speed' => $data['wind']['speed'] ?? 0,
            'wind_deg' => $data['wind']['deg'] ?? 0,
            'clouds' => $data['clouds']['all'] ?? 0,
            'visibility' => $data['visibility'] ?? 0,
            'sunrise' => isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise']) : '',
            'sunset' => isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset']) : '',
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    // Erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Impossible de récupérer la météo',
        'city' => $city,
        'http_code' => $httpCode
    ], JSON_PRETTY_PRINT);
}
