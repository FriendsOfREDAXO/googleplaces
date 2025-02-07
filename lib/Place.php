<?php 

namespace FriendsOfRedaxo\GooglePlaces;

use rex_yform_manager_dataset;
use rex_yform_manager_collection;
class Place extends rex_yform_manager_dataset {
	
    /* Reviews */
    /** @api */
    public function getReviews() :rex_yform_manager_collection {
        return Review::query()
            ->where('place_id', $this->getId())
            ->find();
    }

    /* Place ID */
    /** @api */
    public function getPlaceId() : ?string {
        return $this->getValue("place_id");
    }
    /** @api */
    public function setPlaceId(mixed $value) : self {
        $this->setValue("place_id", $value);
        return $this;
    }

    /* API Response JSON */
    /** @api */
    public function getApiResponseJson(bool $asPlaintext = false) : ?string {
        if($asPlaintext) {
            return strip_tags($this->getValue("api_response_json"));
        }
        return $this->getValue("api_response_json");
    }
    /** @api */
    public function setApiResponseJson(mixed $value) : self {
        $this->setValue("api_response_json", $value);
        return $this;
    }
            
    /* Zuletzt aktualisiert */
    /** @api */
    public function getUpdatedate() : ?string {
        return $this->getValue("updatedate");
    }
    /** @api */
    public function setUpdatedate(string $datetime) : self {
        $this->getValue("updatedate", $datetime);
        return $this;
    }
            
    public static function epYformDataList(\rex_extension_point $ep) {
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
                if($api_json_response) {
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
}
