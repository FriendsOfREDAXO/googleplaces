<?php


// Migration von mf_googleplaces nach googleplaces

// Benenne bestehende Tabellen mf_googleplaces_reviews in googleplaces_reviews um
if (rex_sql_table::get('mf_googleplaces_reviews')) {
    rex_sql_table::get('mf_googleplaces_reviews')
        ->setName(rex::getTablePrefix().'googleplaces_reviews')
        ->alter();
}

if (rex_sql_table::get('mf_googleplaces_place_detail')) {
    rex_sql_table::get('mf_googleplaces_place_detail')
        ->setName(rex::getTablePrefix().'googleplaces_place_detail')
        ->alter();
}

// Config-Werte übernehmen

if (rex_config::get('mf_googleplaces', 'gmaps-api-key') == "") {
    rex_config::set('googleplaces', 'gmaps-api-key', rex_config::get('mf_googleplaces', 'gmaps-api-key'));
}

if (rex_config::get('mf_googleplaces', 'gmaps-location-id') == "") {
    rex_config::set('googleplaces', 'gmaps-location-id', rex_config::get('mf_googleplaces', 'gmaps-location-id'));
}

rex_config::removeNamespace('mf_googleplaces');

include(__DIR__ . '/install/table.php');
include(__DIR__ . '/install/tableset.php');
