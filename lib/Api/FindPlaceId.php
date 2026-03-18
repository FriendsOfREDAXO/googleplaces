<?php

class rex_api_find_place_id extends rex_api_function {
    protected $published = false;

    private const FIELD_MASK = 'places.id,places.displayName,places.formattedAddress';

    public function execute()
    {
        $query = [];
        $query['name'] = rex_request('name', 'string', '');
        $query['street'] = rex_request('street', 'string', '');
        $query['zip'] = rex_request('zip', 'string', '');
        $query['city'] = rex_request('city', 'string', '');
        $apiKey = rex_config::get('googleplaces', 'api_key');
        $searchTerm = trim(implode(' ', $query));
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
        foreach (($result['places'] ?? []) as $place) {
            $return[$place['id']]['text'] = $place['displayName']['text'] ?? '';
            $return[$place['id']]['formattedAddress'] = $place['formattedAddress'] ?? '';
        }


        rex_response::setStatus(rex_response::HTTP_OK);
        rex_response::sendJson($return);
        exit;


    }
    
    public static function queryPlaces($name, $street, $zip, $city, $apiKey)
    {
        // Erstelle die Suchanfrage
        $queryParts = array_filter([$name, $street, $zip, $city], static fn($v) => $v !== '');
        $query = implode(', ', $queryParts);
        if ($query === '') {
            return ['error' => 'No search term given'];
        }

        if ($apiKey === '') {
            return ['error' => 'Google Places API key is missing'];
        }

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
            'X-Goog-FieldMask: ' . self::FIELD_MASK


        ]);


        // Führe die Anfrage aus
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'cURL error: ' . $error];
        }

        $httpStatus = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        // Dekodiere die JSON-Antwort
        $responseData = json_decode($response, true);
        
        // Check if JSON decode was successful
        if ($responseData === null) {
            return ['error' => 'Invalid JSON response from API'];
        }

        if ($httpStatus >= 400 || isset($responseData['error'])) {
            return ['error' => self::extractErrorMessage($responseData, $httpStatus)];
        }

        if (!isset($responseData['places']) || !is_array($responseData['places'])) {
            $responseData['places'] = [];
        }
        
        return $responseData;
    }

    private static function extractErrorMessage(array $responseData, int $httpStatus): string
    {
        $error = $responseData['error'] ?? null;

        if (is_string($error) && $error !== '') {
            return $error;
        }

        if (is_array($error)) {
            $message = trim((string) ($error['message'] ?? ''));
            $status = trim((string) ($error['status'] ?? ''));

            if ($message !== '' && $status !== '') {
                return $status . ': ' . $message;
            }

            if ($message !== '') {
                return $message;
            }

            if ($status !== '') {
                return $status;
            }
        }

        if ($httpStatus >= 400) {
            return 'Google Places API request failed with HTTP status ' . $httpStatus;
        }

        return 'Unknown error while requesting Google Places API';
    }

}
