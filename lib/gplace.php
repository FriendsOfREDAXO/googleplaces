<?php

/**
 * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces instead
 */

use FriendsOfRedaxo\GooglePlaces\GooglePlaces;
use FriendsOfRedaxo\GooglePlaces\Place;

class gplace
{
    /**
     * @return array
     * https://developers.google.com/maps/documentation/places/web-service/details?hl=de
     * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces::googleApiResult instead
     */
    public static function gapi()
    {
        return GooglePlaces::googleApiResult();
    }


    /**
     * Ruft Details zu einem Google Place direkt über die Google-PLaces-API ab.
     * @param string $qry
     * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces::getFromGoogle instead
     */
    public static function get(string $qry = "")
    {
        return GooglePlaces::getFromGoogle($qry);
    }

    /**
     * Ruft Details zu einem Google Place direkt über die Google API ab.
     * @param string $qry
     * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces::getFromGoogle instead
     */
    public static function getFromGoogle(string $qry = "")
    {
        return GooglePlaces::getFromGoogle($qry);
    }

    /**
     * Ruft Details zu einem Google Place über die eigene DB ab.
     * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces::getPlaceDetails instead
     */
    public static function getPlaceDetails($qry = "")
    {
        return GooglePlaces::getPlaceDetails($qry);
    }

    /**
     * Ruft Reviews zu einem Google Place direkt über die Google API ab (wsl. limitiert auf die letzten 5).
     * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces::getAllReviewsLive instead
     */
    public static function getAllReviewsFromGoogle()
    {
        return GooglePlaces::getAllReviewsLive();
    }

    /**
     * Ruft alle Reviews zu einem Google Place aus der eigenen DB ab.
     * @deprecated 3.0.0 Use FriendsOfRedaxo\GooglePlaces\GooglePlaces::getAllReviewsLive or Place::getReviews per Place instead
     */
    public static function getAllReviews(string $orderBy = "", int $limit = null)
    {
        return GooglePlaces::getAllReviewsLive();
    }

    /**
     * Ruft die durschnittliche Bewertung aller Reviews zu einem Google Place aus der eigenen DB ab.
     * @deprecated 3.0.0 Use Place::getAvgRating instead (per Place)
     */
    public static function getAvgRating()
    {
        return '';
    }

    /**
     * Ruft die Anzahl aller Reviews zu einem Google Place aus der eigenen DB ab.
     * @deprecated 3.0.0 Use Place::getAvgRatingDb or Place::getAvgRatingApi instead (per Place)
     */
    public static function getTotalRatings()
    {
        return '';
    }

    /**
     * Holt die Reviews von der Google API und speichert sie in der DB. Wenn der Eintrag bereits vorhanden ist, wird
     * er nicht verändert.
     * @deprecated 3.0.0 Use Place::sync() per Place or GooglePlaces::syncAll() instead
     */
    public static function updateReviewsDB()
    {
        GooglePlaces::syncAll();
    }

}
