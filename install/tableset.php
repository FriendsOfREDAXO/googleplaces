<?php

// Wenn YForm installiert ist, dann die YForm-Tabellen anlegen
if (rex_addon::get('yform')->isAvailable()) {
    // Migration: choice_status zu choice (yform_field Abhängigkeit entfernt)
    $sql = rex_sql::factory();
    $sql->setQuery(
        'DELETE FROM ' . rex::getTable('yform_field') . ' WHERE table_name = :table AND name = :name AND type_name = :type',
        [':table' => 'rex_googleplaces_review', ':name' => 'status', ':type' => 'choice_status']
    );

    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/googleplaces_review.tableset.json', '[]'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/googleplaces_place_detail.tableset.json', '[]'));
    rex_yform_manager_table::deleteCache();
}
