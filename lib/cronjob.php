<?php
namespace gplaces;

# Cronjob-Klasse;

class cronjob extends \rex_cronjob
{

    const LABEL = 'Google Places aktualisieren';

    public function execute() : bool
    {
        \gplace::updateReviewsDB();
        return true;
    }

    public function getTypeName() : string
    {
        return \rex_i18n::msg( 'googleplaces_cron_title' );
    }

}
