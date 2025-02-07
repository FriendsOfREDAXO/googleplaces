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

if (rex_config::get('mf_googleplaces', 'gmaps-api-key') !== null || rex_config::get('mf_googleplaces', 'gmaps-api-key') !== '') {
    rex_config::set('googleplaces', 'gmaps-api-key', rex_config::get('mf_googleplaces', 'gmaps-api-key'));
}

// Einrichtung und Installation

include(__DIR__ . '/install/table.php');
include(__DIR__ . '/install/tableset.php');

if (rex_config::get('mf_googleplaces', 'gmaps-location-id') !== null) {
        
    rex_delete_cache();

    // Get existing place or create new one
    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'googleplaces_place_detail');
    $sql->setValue('place_id', rex_config::get('mf_googleplaces', 'gmaps-location-id'));
    $sql->insertOrUpdate();


    rex_config::set('googleplaces', 'gmaps-location-id', rex_config::get('mf_googleplaces', 'gmaps-location-id'));

}

rex_config::removeNamespace('mf_googleplaces');
