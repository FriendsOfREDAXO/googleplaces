<?php 

namespace FriendsOfRedaxo\GooglePlaces;

use rex_yform_manager_dataset;

class Place extends rex_yform_manager_dataset {
	
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
            
}?>
