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
    public function getReviews(int $limit = 100, int $offset = 0, int $minRating = 5, string $orderByField = 'publishdate', string $orderBy = 'DESC', int $status = Review::STATUS_VISIBLE) :rex_yform_manager_collection
    {
        return Review::query()
            ->where('place_detail_id', $this->getId())
            ->where('rating', $minRating, '>=')
            ->where('status', $status)
            ->limit($offset, $limit)
            ->orderBy($orderByField, $orderBy)
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
        return json_decode($this->getApiResponseJson() ?: '', true);
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
        $this->setValue("updatedate", $datetime);
        return $this;
    }
            
    public static function epYformDataList(\rex_extension_point $ep)
    {
        if ($ep->getParam('table')->getTableName() !== self::table()->getTableName()) {
            return;
        }

        /** @var \rex_yform_list $list */
        $list = $ep->getSubject();

        $list->setColumnFormat(
            'place_id',
            'custom',
            static function ($a) {
                $api_json_response = \json_decode($a['list']->getValue('api_response_json'));
                $output = "<code>" . $a['value'] . "</code>";
                if ($api_json_response !== null) {
                    $url = $api_json_response->url ?? '#';
                    $name = $api_json_response->name ?? 'Unknown';
                    $output = '<strong><a href="'.$url.'" target="_blank">' . $name. '</a></strong>';
                    if (isset($api_json_response->formatted_address)) {
                        $output .= '<br>' . $api_json_response->formatted_address;
                    }
                    if (isset($api_json_response->formatted_phone_number)) {
                        $output .= '<br>' . $api_json_response->formatted_phone_number;
                    }
                    $output .= '<br><i class="fa fa-image"></i> ×' . count($api_json_response->photos ?? []);
                    if (isset($api_json_response->rating) && isset($api_json_response->user_ratings_total)) {
                        $output .= '<br><i class="fa fa-star"></i> ' . $api_json_response->rating ." (".$api_json_response->user_ratings_total .")";
                    }
                    $output .= "<br><code>" . $a['value'] . "</code>";

                }
                return $output;
            },
        );
        $list->setColumnFormat(
            'api_response_json',
            'custom',
            static function ($a) {
                return '<textarea rows="6" disabled cols="30">' . $a['value'] . "</textarea>";
            },
        );

        // updatedate formatiert ausgeben mit rex_formatter
        $list->setColumnFormat(
            'updatedate',
            'custom',
            static function ($a) {
                return '<span class="text-nowrap">'. \rex_formatter::intlDateTime($a['value']) .'</span>';
            },
        );

        
    }
    
    /** @api */
    public function sync() : bool
    {

        
        $success = false;

        $googlePlace = GooglePlaces::getFromGoogle($this->getPlaceId());
        
        // Check for API errors
        if (isset($googlePlace['error'])) {
            $errorMsg = 'Google Places API Error for Place ID ' . $this->getPlaceId() . ': ' . $googlePlace['error'];
            \rex_logger::factory()->log('warning', $errorMsg, [], __FILE__, __LINE__);
            return false;
        }
        
        if (empty($googlePlace)) {
            \rex_logger::factory()->log('warning', 'Google Place not found for Place ID: ' . $this->getPlaceId(), [], __FILE__, __LINE__);
            return false;
        }
        
        // Check if reviews exist in the response
        if (!isset($googlePlace['reviews'])) {
            \rex_logger::factory()->log('info', 'No reviews found for Place ID: ' . $this->getPlaceId(), [], __FILE__, __LINE__);
            // Still update place details even if no reviews
            $this
                ->setApiResponseJson(json_encode($googlePlace, \JSON_PRETTY_PRINT))
                ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'));
            return $this->save();
        }
        
        $reviews_from_api = $googlePlace['reviews'];
        
        // Update place details
        $this
            ->setApiResponseJson(json_encode($googlePlace, \JSON_PRETTY_PRINT))
            ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'));

        // Save place
        $success = $this->save();

        // Check if review synchronization is enabled
        $addon = \rex_addon::get('googleplaces');
        $syncReviews = $addon->getConfig('sync_reviews', true);
        
        if (!$syncReviews) {
            // Reviews are disabled, return early
            return $success;
        }

        if ($success) {

            $place_dataset_id = $this->getId();
            $place_id = $this->getPlaceId();


            foreach ($reviews_from_api as $review_from_api) {
                $uuid = rex_yform_value_uuid::guidv4(md5($place_id . $review_from_api['author_url']));

                // Statt SQL-Query via rex_sql, den Eintrag über Review-Model prüfen
                $review = Review::query()
                    ->where('uuid', $uuid)
                    ->findOne();

                if (!$review) {
                    // Neuen Review anlegen
                    $review = Review::create()
                    ->setCreatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'));
                    // Set initial status based on configuration
                    $autoPublish = $addon->getConfig('auto_publish_reviews', false);
                    $initialStatus = $autoPublish ? Review::STATUS_VISIBLE : Review::STATUS_HIDDEN;
                    $review->setStatus($initialStatus);
                }

                $review->setPlaceId($place_dataset_id);

                // Download and save profile photo to filesystem
                $profile_photo_filename = null;
                if (!empty($review_from_api['profile_photo_url'])) {
                    $profile_photo_data = @file_get_contents($review_from_api['profile_photo_url']);
                    if ($profile_photo_data !== false) {
                        // Create directory if it doesn't exist
                        $photo_dir = \rex_path::addonData('googleplaces', 'profile_photos/');
                        \rex_dir::create($photo_dir);
                        
                        // Generate filename from UUID to ensure uniqueness
                        $profile_photo_filename = $uuid . '.jpg';
                        $photo_path = $photo_dir . $profile_photo_filename;
                        
                        // Save the photo to filesystem
                        if (\rex_file::put($photo_path, $profile_photo_data) !== false) {
                            // Successfully saved to filesystem
                        } else {
                            // Failed to save, clear filename
                            $profile_photo_filename = null;
                        }
                    }
                }

                // Review-Daten über Model-Methoden setzen
                $review->setAuthorName($review_from_api['author_name'])
                    ->setAuthorUrl($review_from_api['author_url'])
                    ->setRating($review_from_api['rating'])
                    ->setText($review_from_api['text'])
                    ->setProfilePhotoUrl($review_from_api['profile_photo_url'])
                    ->setProfilePhotoFile($profile_photo_filename)
                    ->setGooglePlaceId($this->getPlaceId())
                    ->setPublishdate((new DateTime('@' . $review_from_api['time']))->format('Y-m-d H:i:s'))
                    ->setUpdatedate((new DateTime('NOW'))->format('Y-m-d H:i:s'))
                    ->setUuid($uuid);

                // Review speichern
                $success = $review->save();
            }
        }
        return $success;
    }

    /** @api */
    public function countReviews() : int
    {
        return Review::query()
            ->where('place_detail_id', $this->getId())
            ->count();
    }

    /** @api */
    public function getAvgRatingDb() : float
    {
        $reviews = $this->getReviews(0, 0, 0);
        $rating = 0;
        $i = 0;
        foreach ($reviews as $review) {
            /** @var Review $review */
            $rating += $review->getRating();
            $i++;
        }
        if ($i === 0) {
            return 0;
        }
        return $rating / $i;
    }

    /** @api */
    public function getAvgRatingApi() : float
    {
        $googlePlace = $this->getApiResponseAsArray();
        if (!isset($googlePlace['rating'])) {
            return 0;
        }
        return $googlePlace['rating'];
    }

    // Place-Name aus JSON auslesen
    /** @api */
    public function getName() : string
    {
        $googlePlace = $this->getApiResponseAsArray();
        if (!isset($googlePlace['name'])) {
            return '';
        }
        return $googlePlace['name'];
    }

    // Place-Adresse aus JSON auslesen
    /** @api */
    public function getAddress() : string
    {
        $googlePlace = $this->getApiResponseAsArray();
        if (!isset($googlePlace['formatted_address'])) {
            return '';
        }
        return $googlePlace['formatted_address'];
    }

    // Places-Einträge als $id => $name . " " . $address Array ausgeben
    /** @api */
    public static function getPlacesOptions() : array
    {
        $places = [];
        foreach (self::query()->find() as $place) {
            /** @var Place $place */
            $places[$place->getId()] = $place->getName() . " " . $place->getAddress();
        }
        return $places;
    }

}
