<?php

namespace FriendsOfRedaxo\GooglePlaces;

class Cronjob extends \rex_cronjob
{

    public function execute() : bool
    {
        $addon = \rex_addon::get('googleplaces');
        
        // Check if API key is configured
        if (empty($addon->getConfig('api_key'))) {
            $this->setMessage('No API key configured. Please add an API key in the addon settings.');
            return false;
        }
        
        $result = GooglePlaces::syncAll();
        
        if (!$result) {
            $this->setMessage('Error syncing Google Places data. Check the system log for details.');
        }
        
        return $result;
    }

    public function getTypeName() : string
    {
        return \rex_i18n::msg('googleplaces_cron_title');
    }

}
