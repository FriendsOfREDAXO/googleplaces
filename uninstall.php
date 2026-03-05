<?php

// YForm-Tabellen entfernen
if (rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_table_api::removeTable(rex::getTable('googleplaces_review'));
    rex_yform_manager_table_api::removeTable(rex::getTable('googleplaces_place_detail'));
}

// Datenbank-Tabellen entfernen
rex_sql_table::get(rex::getTable('googleplaces_review'))->drop();
rex_sql_table::get(rex::getTable('googleplaces_place_detail'))->drop();

// Cronjob entfernen
if (rex_addon::get('cronjob')->isAvailable()) {
    $sql = rex_sql::factory();
    $sql->setQuery(
        'DELETE FROM ' . rex::getTable('cronjob') . ' WHERE `type` = :class',
        [':class' => 'FriendsOfRedaxo\\GooglePlaces\\Cronjob']
    );
}

// Profilbilder-Verzeichnis entfernen
rex_dir::delete(rex_path::addonData('googleplaces'));

// Config entfernen
rex_config::removeNamespace('googleplaces');
