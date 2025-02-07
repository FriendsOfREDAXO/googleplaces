<?php

namespace FriendsOfRedaxo\GooglePlaces;

use rex_sql;
use rex_var;
use rex_addon;
use DateTime;
use rex_yform_value_uuid;

/**
 * Google Places Add-on: Gibt Details zu einem Google Place aus.
 */
class Helper
{


    /**
     * @return array
     * https://developers.google.com/maps/documentation/places/web-service/details?hl=de
     */
    public static function gapi(string $place_id = null): array
    {

        if ($place_id == null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&key=' . rex_addon::get('googleplaces')->getConfig('gmaps-api-key') . '&reviews_no_translations=true&reviews_sort=newest',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        $response = json_decode($response);
        $response = json_decode(json_encode($response->result), true);
        curl_close($curl);
        return $response;
    }

    /**
     * Ruft Details zu einem Google Place direkt über die Google API ab.
     * @param string $place_id
     * @return array | string
     * @author Daniel Springer
     */
    public static function getFromGoogle(string $place_id = null): array | string
    {
        if ($place_id === null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }
        $response = self::gapi($place_id);
        if ($place_id == "") {
            return $response;
        } else {
            return $response[$place_id];
        }
    }

    /**
     * Ruft Details zu einem Google Place über die eigene DB ab.
     * @return array | false
     * @author Daniel Springer
     */
    public static function getPlaceDetails($place_id = null): array | false
    {

        if ($place_id === null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }

        $place = Place::query()
            ->where('place_id', $place_id)
            ->findOne();

        if ($place) {
            return json_decode($place->getApiResponseJson(), true);
        } else {
            return false;
        }
    }

    /**
     * Ruft Reviews zu einem Google Place direkt über die Google API ab (wsl. limitiert auf die letzten 5).
     * @return array
     * @author Daniel Springer
     */
    public static function getAllReviewsFromGoogle()
    {
        $qry = 'reviews';
        $response = self::gapi();
        return $response['result'][$qry];
    }

    /**
     * Ruft alle Reviews zu einem Google Place aus der eigenen DB ab.
     * @return array
     * @author Daniel Springer
     */
    public static function getAllReviews(string $orderBy = "", int $limit = null): array
    {
        $sql = rex_sql::factory();
        $qry = 'SELECT * FROM rex_googleplaces_review';

        if ($orderBy != "") {
            $qry .= ' ORDER BY ' . $orderBy;
        }
        if ($limit != "") {
            $qry .= ' LIMIT ' . $limit;
        }
        $sql->setQuery($qry);

        $response = [];
        foreach ($sql as $row) {
            $id = $row->getValue('id');
            $response[$id]['id'] = $row->getValue('id');
            $response[$id]['author_name'] = $row->getValue('author_name');
            $response[$id]['author_url'] = $row->getValue('author_url');
            $response[$id]['language'] = $row->getValue('language');
            $response[$id]['profile_photo_url'] = $row->getValue('profile_photo_url');
            $response[$id]['profile_photo_base64'] = $row->getValue('profile_photo_base64');
            $response[$id]['rating'] = $row->getValue('rating');
            $response[$id]['text'] = $row->getValue('text');
            $response[$id]['profile_photo_url'] = $row->getValue('profile_photo_url');
            $response[$id]['time'] = $row->getValue('time');
            $response[$id]['createdate'] = $row->getValue('createdate');
            $response[$id]['google_place_id'] = $row->getValue('google_place_id');
        }
        return $response;
    }

    /**
     * Ruft die durschnittliche Bewertung aller Reviews zu einem Google Place aus der eigenen DB ab.
     * @return float
     * @author Daniel Springer
     */
    public static function getAvgRating(): float
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT rating FROM rex_googleplaces_review');
        $rating = 0;
        $i = $sql->getRows();
        foreach ($sql as $row) {
            $rating = $rating + $row->getValue('rating');
        }
        return round(floatval($rating / $i), 1);
    }

    /**
     * Ruft die Anzahl aller Reviews zu einem Google Place aus der eigenen DB ab.
     * @return int
     * @author Daniel Springer
     */
    public static function getTotalRatings(): int
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_googleplaces_review');
        $i = $sql->getRows();
        return $i;
    }

    /**
     * Holt die Reviews von der Google API und speichert sie in der DB. Wenn der Eintrag bereits vorhanden ist, wird
     * er nicht verändert.
     * @return bool
     * @author Daniel Springer, Alexander Walther
     */
    public static function updateReviewsDB(): bool
    {
        $googlePlace    = self::getFromGoogle();
        $googleReviews  = $googlePlace['reviews'];
        $googlePlaceId  = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');

        $success = false;

        // Get existing place or create new one
        $place = Place::query()
            ->where('place_id', $googlePlaceId)
            ->findOne();

        if (!$place) {
            $place = Place::create();
            $place->setPlaceId($googlePlaceId);
        }
        // Update place details
        $place->setApiResponseJson(json_encode($googlePlace, \JSON_PRETTY_PRINT))
            ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'));

        // Save place
        $success = $place->save();

        if ($success) {

            $place_dataset_id = $place->getId();

            foreach ($googleReviews as $gr) {
                $uuid = rex_yform_value_uuid::guidv4($gr['author_url']);

                // Statt SQL-Query via rex_sql, den Eintrag über Review-Model prüfen
                $review = Review::query()
                    ->where('uuid', $uuid)
                    ->findOne();

                if (!$review) {
                    // Neuen Review anlegen
                    $review = Review::create()
                    ->setPlaceId($place_dataset_id)
                    ->setCreatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'));
                }

                // Base64 Profilbild holen wenn verfügbar
                $gr_profile_photo_base64 = @file_get_contents($gr['profile_photo_url']);
                if ($gr_profile_photo_base64 !== false) {
                    $gr_profile_photo_base64 = base64_encode($gr_profile_photo_base64);
                }

                // Review-Daten über Model-Methoden setzen
                $review->setAuthorName($gr['author_name'])
                    ->setAuthorUrl($gr['author_url'])
                    ->setRating($gr['rating'])
                    ->setText($gr['text'])
                    ->setTime($gr['time'])
                    ->setProfilePhotoUrl($gr['profile_photo_url'])
                    ->setProfilePhotoBase64($gr_profile_photo_base64)
                    ->setGooglePlaceId($googlePlaceId)
                    ->setPublishedate((new DateTime('@' . $gr['time']))->format('Y-m-d H:i:s'))
                    ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'))
                    ->setUuid($uuid);

                // Review speichern
                $success = ($review->save() ?: $success);
            }
        }
        return $success;
    }
}
