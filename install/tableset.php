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

    // Optionales Feld: Zuordnung eines Place zu einer oder mehreren
    // YRewrite-Domains. Wird nur angelegt, wenn YRewrite verfügbar ist —
    // setTableField() upsertet anhand von (type_id, name) und ist damit
    // bei wiederholter Installation idempotent.
    if (rex_addon::get('yrewrite')->isAvailable()) {
        rex_yform_manager_table_api::setTableField('rex_googleplaces_place_detail', [
            'table_name' => 'rex_googleplaces_place_detail',
            'prio' => 7,
            'type_id' => 'value',
            'type_name' => 'choice',
            'db_type' => 'text',
            'list_hidden' => 0,
            'search' => 0,
            'name' => 'domain_ids',
            'label' => 'translate:googleplaces_place_detail.domain_ids',
            'not_required' => '1',
            'choices' => 'SELECT `id` AS `value`, `domain` AS `label` FROM ' . rex::getTable('yrewrite_domain') . ' ORDER BY `domain`',
            'expanded' => '1',
            'multiple' => '1',
            'default' => '',
            'no_db' => '0',
            'notice' => 'translate:googleplaces_place_detail.domain_ids.notice',
        ]);
        $placeDetailTable = rex_yform_manager_table::get('rex_googleplaces_place_detail');
        if ($placeDetailTable) {
            rex_yform_manager_table_api::generateTableAndFields($placeDetailTable);
        }
    }

    rex_yform_manager_table::deleteCache();
}
