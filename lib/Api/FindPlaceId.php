<?php

use rex_config;
use rex_response; 
use rex_api_function;

class rex_api_find_place_id extends rex_api_function {
    protected $published = true;

    public function execute()
    {
        $query = [];
        $query['name'] = rex_request('name', 'string', '');
        $query['street'] = rex_request('street', 'string', '');
        $query['zip'] = rex_request('zip', 'string', '');
        $query['city'] = rex_request('city', 'string', '');
        $apiKey = rex_config::get('googleplaces', 'api_key');
        $searchTerm = implode(' ', $query);
        rex_response::cleanOutputBuffers();
        rex_response::sendContentType('application/json; charset=utf-8');

        if ($searchTerm === '') {
            rex_response::setStatus(rex_response::HTTP_BAD_REQUEST);
            $error = ['error' => 'No search term given'];
            echo json_encode($error);
            exit;
        }

        $result = self::queryPlaces($query['name'], $query['street'], $query['zip'], $query['city'], $apiKey);

        if (isset($result['error'])) {
            rex_response::setStatus(rex_response::HTTP_INTERNAL_ERROR);
            echo json_encode($result);
            exit;
        }

        $return = [];
        foreach($result['places'] as $place) {
                $return[$place['id']]['text'] = $place['displayName']['text'];
                $return[$place['id']]['formattedAddress'] = $place['formattedAddress'];
        }


        rex_response::setStatus(rex_response::HTTP_OK);
        rex_response::sendJson($return);
        exit;


    }
    
    public static function queryPlaces($name, $street, $zip, $city, $apiKey)
    {
        // Erstelle die Suchanfrage
        $quarry = [$name, $street, $zip, $city];
        $query = implode(', ', $quarry);
        $url = "https://places.googleapis.com/v1/places:searchText?key=$apiKey";


        // Erstelle den JSON-Request-Body
        $requestBody = json_encode([
            "textQuery" => $query,
            "languageCode" => "de",
        ]);


        // Initialisiere cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody); // Request Body mit Location
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($requestBody),
            'X-Goog-Api-Key: ' . $apiKey,
            'X-Goog-FieldMask: *'


        ]);


        // Führe die Anfrage aus
        $response = curl_exec($ch);


        // Schließe cURL
        curl_close($ch);

        // Dekodiere die JSON-Antwort
        $responseData = json_decode($response, true);
        return $responseData;
    }

}
