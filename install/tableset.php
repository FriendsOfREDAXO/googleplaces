<?php

// Wenn YForm Addon installiert ist, dann Tablesets importieren

if (rex_addon::exists('yform') && rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/rex_googleplaces_place_detail.tableset.json', '[]'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/rex_googleplaces_review.tableset.json', '[]'));
}
