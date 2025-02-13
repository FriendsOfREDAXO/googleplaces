<?php

namespace FriendsOfRedaxo\GooglePlaces;

use rex_addon;

/**
 * Google Places Add-on: Gibt Details zu einem Google Place aus.
 */
class GooglePlaces
{


    /**
     * @api
     * @return array
     * https://developers.google.com/maps/documentation/places/web-service/details?hl=de
     */
    public static function googleApiResult(string $place_id = null): array
    {

        if ($place_id === null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&key=' . rex_addon::get('googleplaces')->getConfig('api_key') . '&reviews_no_translations=true&reviews_sort=newest',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        $json_response = json_decode($response);

        $array_response = json_decode(json_encode($json_response->result), true);
        curl_close($curl);

        return $array_response ?? [];
    }

    /**
     * @api
     * Ruft Details zu einem Google Place direkt über die Google API ab.
     * @param string $place_id
     * @return array | string
     * @author Daniel Springer
     */
    public static function getFromGoogle(string $place_id = null, string $key = null): array | string
    {
        if ($place_id === null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }
        $response = self::googleApiResult($place_id);

        if ($key === null) {
            return $response;
        } else {
            return $response[$key];
        }
    }

    /**
     * @api
     * Ruft Details zu einem Google Place ab.
     * @return array | false
     * @author Daniel Springer
     */
    public static function getPlaceDetails(string $place_id = null): array | string | false
    {

        if ($place_id === null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }

        $place = Place::query()
            ->where('place_id', $place_id)
            ->findOne();

        if ($place) {
            return json_decode($place->getApiResponseJson(), true);
        }
        if ($place_id) {
            return self::getFromGoogle($place_id);
        }
        return false;
    }

    /**
     * @api
     * Ruft die 5 letzten Reviews zu einem Google Place direkt über die Google API ab.
     * @return array
     * @author Daniel Springer
     */
    public static function getAllReviewsLive(string $place_id = null): array
    {
        $response = self::googleApiResult($place_id);
        return $response['reviews'];
    }

    /**
     * @api
     * Holt die Reviews von der Google API und speichert sie in der DB. Wenn der Eintrag bereits vorhanden ist, wird
     * er nicht verändert.
     * @return bool
     * @author Daniel Springer, Alexander Walther
     */
    public static function syncAll(): bool
    {
        $places = Place::query()->find();
        $success = false;

        foreach ($places as $place) {
            /** @var Place $place */
            $success = $place->sync();
        }
        return $success;
    }
}
