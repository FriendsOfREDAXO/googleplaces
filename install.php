<?php


// Migration von mf_googleplaces nach googleplaces

// Benenne bestehende Tabellen mf_googleplaces_reviews in rex_googleplaces_review und
// mf_googleplaces_place_details in rex_googleplaces_place_detail um

use FriendsOfRedaxo\GooglePlaces\Place;
use FriendsOfRedaxo\GooglePlaces\Review;

$table = rex_sql_table::get('mf_googleplaces_reviews');
if ($table->exists()) {
    rex_sql_table::get('mf_googleplaces_reviews')
        ->setName(rex::getTablePrefix().'googleplaces_review')
        ->alter();
}
$table = rex_sql_table::get('mf_googleplaces_place_details');
if ($table->exists()) {
    rex_sql_table::get('mf_googleplaces_place_details')
        ->setName(rex::getTablePrefix().'googleplaces_place_detail')
        ->alter();
}

// Config-Werte übernehmen

if (rex_config::get('mf_googleplaces', 'gmaps-api-key') !== "") {
    rex_config::set('googleplaces', 'gmaps-api-key', rex_config::get('mf_googleplaces', 'gmaps-api-key'));
}

// Einrichtung und Installation

include(__DIR__ . '/install/table.php');
include(__DIR__ . '/install/tableset.php');

if (rex_config::get('mf_googleplaces', 'gmaps-location-id') !== "") {
        
    rex_delete_cache();

        
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

    // Get existing place or create new one
    $place = Place::query()
        ->where('place_id', rex_config::get('mf_googleplaces', 'gmaps-location-id'))
        ->findOne();
    if ($place !== null) {
        $place = Place::create();
        $place->setPlaceId(rex_config::get('mf_googleplaces', 'gmaps-location-id'));
        $place->save();
    }

    rex_config::set('googleplaces', 'gmaps-location-id', rex_config::get('mf_googleplaces', 'gmaps-location-id'));

}

rex_config::removeNamespace('mf_googleplaces');
