<?php

namespace FriendsOfRedaxo\GooglePlaces;

class Cronjob extends \rex_cronjob
{

    public function execute() : bool
    {
        return GooglePlaces::syncAll();
    }

    public function getTypeName() : string
    {
        return \rex_i18n::msg('googleplaces_cron_title');
    }

}
