<?php

namespace FriendsOfRedaxo\GooglePlaces;

use DateTime;
use rex_yform_manager_dataset;
use rex_yform_manager_collection;
use rex_yform_value_uuid;

class Place extends rex_yform_manager_dataset
{
    
    /* Reviews */
    /** @api */
    public function getReviews() :rex_yform_manager_collection
    {
        return Review::query()
            ->where('place_id', $this->getId())
            ->find();
    }

    /* Place ID */
    /** @api */
    public function getPlaceId() : ?string
    {
        return $this->getValue("place_id");
    }
    /** @api */
    public function setPlaceId(mixed $value) : self
    {
        $this->setValue("place_id", $value);
        return $this;
    }

    /* API Response JSON */
    /** @api */
    public function getApiResponseJson() : ?string
    {
        return $this->getValue("api_response_json");
    }

    public function getApiResponseAsArray() : ?array
    {
        return json_decode($this->getApiResponseJson(), true);
    }

    /** @api */
    public function setApiResponseJson(mixed $value) : self
    {
        $this->setValue("api_response_json", $value);
        return $this;
    }
            
    /* Zuletzt aktualisiert */
    /** @api */
    public function getUpdatedate() : ?string
    {
        return $this->getValue("updatedate");
    }
    /** @api */
    public function setUpdatedate(string $datetime) : self
    {
        $this->getValue("updatedate", $datetime);
        return $this;
    }
            
    public static function epYformDataList(\rex_extension_point $ep)
    {
        if ($ep->getParam('table')->getTableName() !== self::table()->getTableName()) {
            return;
        }

        /** @var rex_yform_list $list */
        $list = $ep->getSubject();

        $list->setColumnFormat(
            'place_id',
            'custom',
            static function ($a) {
                $api_json_response = \json_decode($a['list']->getValue('api_response_json'));
                $output = "<code>" . $a['value'] . "</code>";
                if ($api_json_response) {
                    $output = '<strong><a href="'.$api_json_response->url.'" target="_blank">' . $api_json_response->name. '</a></strong>';
                    $output .= '<br>' . $api_json_response->formatted_address;
                    $output .= '<br>' . $api_json_response->formatted_phone_number;
                    $output .= '<br><i class="fa fa-image"></i> ×' . count($api_json_response->photos);
                    $output .= '<br><i class="fa fa-star"></i> ' . $api_json_response->rating ." (".$api_json_response->user_ratings_total .")";
                    $output .= "<br><code>" . $a['value'] . "</code>";

                }
                return $output;
            },
        );
        $list->setColumnFormat(
            'api_response_json',
            'custom',
            static function ($a) {
                return "<textarea rows=10 disabled cols=50>" . $a['value'] . "</textarea>";
            },
        );

        // updatedate formatiert ausgeben mit rex_formatter
        $list->setColumnFormat(
            'updatedate',
            'custom',
            static function ($a) {
                return \rex_formatter::strftime($a['value'], 'datetime');
            },
        );

        
    }
    public function sync() {

        
        $googlePlace = Helper::getFromGoogle($this->getPlaceId());
        $reviews_from_api = $googlePlace['reviews'];

        $success = false;
        
        // Update place details
        $this
            ->setApiResponseJson(json_encode($googlePlace, \JSON_PRETTY_PRINT))
            ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'));

        // Save place
        $success = $this->save();

        if ($success) {

            $place_dataset_id = $this->getId();

            foreach ($reviews_from_api as $review_from_api) {
                $uuid = rex_yform_value_uuid::guidv4($place_dataset_id . $review_from_api['author_url']);

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
                $review_profile_photo_base64 = @file_get_contents($review_from_api['profile_photo_url']);
                if ($review_profile_photo_base64 !== false) {
                    $review_profile_photo_base64 = base64_encode($review_profile_photo_base64);
                }

                // Review-Daten über Model-Methoden setzen
                $review->setAuthorName($review_from_api['author_name'])
                    ->setAuthorUrl($review_from_api['author_url'])
                    ->setRating($review_from_api['rating'])
                    ->setText($review_from_api['text'])
                    ->setTime($review_from_api['time'])
                    ->setProfilePhotoUrl($review_from_api['profile_photo_url'])
                    ->setProfilePhotoBase64($review_profile_photo_base64)
                    ->setGooglePlaceId($this->getPlaceId())
                    ->setPublishedate((new DateTime('@' . $review_from_api['time']))->format('Y-m-d H:i:s'))
                    ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'))
                    ->setUuid($uuid);

                // Review speichern
                $success = $review->save();
            }
        }
        return $success;
    }

}
