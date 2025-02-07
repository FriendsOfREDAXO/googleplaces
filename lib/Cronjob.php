<?php

namespace FriendsOfRedaxo\GooglePlaces;

class Cronjob extends \rex_cronjob
{

    const LABEL = 'Google Places aktualisieren';

    public function execute() : bool
    {
        Helper::updateReviewsDB();
        return true;
    }

    public function getTypeName() : string
    {
        return \rex_i18n::msg('googleplaces_cron_title');
    }

}
