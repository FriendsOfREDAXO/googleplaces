<?php

use FriendsOfRedaxo\GooglePlaces\Place;
use FriendsOfRedaxo\GooglePlaces\Review;
use FriendsOfRedaxo\GooglePlaces\Api\rex_api_review_status;

if (rex_addon::get('cronjob')->isAvailable()) {
    \rex_cronjob_manager::registerType('FriendsOfRedaxo\GooglePlaces\Cronjob');
}

rex_api_function::register('review_status', rex_api_review_status::class);

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_googleplaces_place_detail',
        Place::class
    );

    rex_yform_manager_dataset::setModelClass(
        'rex_googleplaces_review',
        Review::class
    );
}

if (in_array(rex_be_controller::getCurrentPagePart(1), ['yform', 'googleplaces'], true)) {
    rex_extension::register('YFORM_DATA_LIST', Place::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST', Review::epYformDataList(...));
}
