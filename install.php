<?php


// Migration von mf_googleplaces nach googleplaces

// Benenne bestehende Tabellen mf_googleplaces_reviews in googleplaces_reviews um

use FriendsOfRedaxo\GooglePlaces\Place;

try {
    rex_sql_table::get('mf_googleplaces_reviews')
        ->setName(rex::getTablePrefix().'googleplaces_reviews')
        ->alter();
} catch (rex_sql_exception $e) {
    // Table does not exist
}

try {
    rex_sql_table::get('mf_googleplaces_place_details')
        ->setName(rex::getTablePrefix().'googleplaces_place_details')
        ->alter();
} catch (rex_sql_exception $e) {
    // Table does not exist
}

// Config-Werte übernehmen

if (rex_config::get('mf_googleplaces', 'gmaps-api-key') !== "") {
    rex_config::set('googleplaces', 'gmaps-api-key', rex_config::get('mf_googleplaces', 'gmaps-api-key'));
}

if (rex_config::get('mf_googleplaces', 'gmaps-location-id') !== "") {
    
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

// Einrichtung und Installation

include(__DIR__ . '/install/table.php');
include(__DIR__ . '/install/tableset.php');
