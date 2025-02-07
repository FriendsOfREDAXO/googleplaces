<?php

// Wenn YForm installiert ist, dann die YForm-Tabellen anlegen

if (rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/googleplaces_review.tableset.json', '[]'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/googleplaces_place_detail.json', '[]'));
    rex_yform_manager_table::deleteCache();
}
