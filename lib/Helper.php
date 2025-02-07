<?php

namespace FriendsOfRedaxo\GooglePlaces;

use rex_sql;
use rex_var;
use rex_addon;
use DateTime;


/**
 * Google Places AddOn: Gibt Details zu einem Google Place aus.
 */
class Helper
{


    /**
     * @return array
     * https://developers.google.com/maps/documentation/places/web-service/details?hl=de
     */
    public static function gapi(string $place_id = null) : array
    {

        if($place_id == null) {
            $place_id = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/details/json?place_id='.$place_id.'&key='.rex_addon::get('googleplaces')->getConfig('gmaps-api-key').'&reviews_no_translations=true&reviews_sort=newest',
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
     * Ruft Details zu einem Google Place direkt über die Google-PLaces-API ab.
     * @param string $qry
     * @return array | string
     * @author Daniel Springer
     */
    public static function get(string $qry = "") : array
    {
        return self::getFromGoogle($qry);
    } // EoF

    /**
     * Ruft Details zu einem Google Place direkt über die Google API ab.
     * @param string $qry
     * @return array | string
     * @author Daniel Springer
     */
    public static function getFromGoogle(string $qry = "")
    {
        $response = self::gapi();
        if ($qry == "") {
            return $response;
        } else {
            return $response[$qry];
        }

    } // EoF

    /**
     * Ruft Details zu einem Google Place über die eigene DB ab.
     * @return array | false
     * @author Daniel Springer
     */
    public static function getPlaceDetails($qry = "") : array | false
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT api_response_json FROM googleplaces_place_detail WHERE place_id = :place_id', ["place_id" => rex_addon::get('googleplaces')->getConfig('gmaps-location-id')]);
        if ($sql->getRows() > 0) {
            $response = $sql->getArray();
            $response = rex_var::toArray($response[0]['api_response_json']);
            if ($qry == "") {
                return $response;
            } else {
                return $response[$qry];
            }
        }
        return false;
    } // EoF getPlaceDetails

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
    } // EoF

    /**
     * Ruft alle Reviews zu einem Google Place aus der eigenen DB ab.
     * @return array
     * @author Daniel Springer
     */
    public static function getAllReviews(string $orderBy = "", int $limit = null) : array
    {
        $sql = rex_sql::factory();
        $qry = 'SELECT * FROM rex_googleplaces_review';

        if ($orderBy != "") {
            $qry .= ' ORDER BY '.$orderBy;
        }
        if ($limit != "") {
            $qry .= ' LIMIT '.$limit;
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
    } // EoF

    /**
     * Ruft die durschnittliche Bewertung aller Reviews zu einem Google Place aus der eigenen DB ab.
     * @return float
     * @author Daniel Springer
     */
    public static function getAvgRating() : float
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT rating FROM rex_googleplaces_review');
        $rating = 0;
        $i = $sql->getRows();
        foreach ($sql as $row) {
            $rating = $rating + $row->getValue('rating');
        }
        return round(floatval($rating/$i), 1);
    } // EoF

    /**
     * Ruft die Anzahl aller Reviews zu einem Google Place aus der eigenen DB ab.
     * @return int
     * @author Daniel Springer
     */
    public static function getTotalRatings() : int
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_googleplaces_review');
        $i = $sql->getRows();
        return $i;
    } // EoF

    /**
     * Holt die Reviews von der Google API und speichert sie in der DB. Wenn der Eintrag bereits vorhanden ist, wird
     * er nicht verändert.
     * @return bool
     * @author Daniel Springer
     */
    public static function updateReviewsDB() : bool
    {
        $googlePlace    = self::getFromGoogle();
        $googleReviews  = $googlePlace['reviews'];
        $googlePlaceId  = rex_addon::get('googleplaces')->getConfig('gmaps-location-id');

        foreach ($googleReviews as $gr) {
            $googleTime = (string)$gr['time'];
            #dump($googleTime);

            $sql_search = rex_sql::factory();
            $sql_search->setDebug(false);
            $sql_search->setQuery('SELECT * FROM rex_googleplaces_review WHERE time = :time', ['time' => $googleTime]);
            #dump($sql_search->getRows());

            if ($sql_search->getRows() == 0) {
                // Wenn Review noch nicht existiert, in DB schreiben
                $objDateTime = new DateTime('NOW');
                $dateTime = $objDateTime->format('Y-m-d H:i:s');

                $gr_profile_photo_base64 = file_get_contents($gr['profile_photo_url']);
                if ($gr_profile_photo_base64 !== false) {
                    $gr_profile_photo_base64 = base64_encode($gr_profile_photo_base64);
                }
                $sql = rex_sql::factory();
                $sql->setDebug(false);
                $sql->setTable('rex_googleplaces_review');
                $sql->setValues(
                    [
                        'author_name' => $gr['author_name'],
                        'author_url' => $gr['author_url'],
//                        'language' => $gr['language'],
                        'rating' => $gr['rating'],
                        'text' => $gr['text'],
                        'time' => $gr['time'],
                        'profile_photo_url' => $gr['profile_photo_url'],
                        'profile_photo_base64' => $gr_profile_photo_base64,
                        'google_place_id' => $googlePlaceId,
                        'createdate' => $dateTime,
                        'uuid' => rand(1, 999999999)
                    ]
                );
                $sql->insert();
            } else {
                #echo 'Review existiert in DB bereits';
            }
        }

        $sql_place = rex_sql::factory();
        $sql_place->setTable('rex_googleplaces_place_detail');
        $sql_place->setValues(
            [
                'updatedate' => date('Y-m-d H:i:s'),
                'place_id' => $googlePlaceId,
                'api_response_json' => json_encode($googlePlace)
            ]
        );
        $sql_place->setWhere([
            'place_id' => $googlePlaceId
        ]);
        // InsertOrUpdate braucht einen unique_key, mit dem das funktionieren kann. Dazu muss man die Tabelle entsprechend in der jeweiligen Spalte anpassen mit:
        // ALTER TABLE `tabellen_name` ADD UNIQUE `unique_index`(`keys_die`, `unique_werden`, `sollen`, `key`);
        $sql_place->insertOrUpdate();

        return true;
    } // EoF

} // EoC
