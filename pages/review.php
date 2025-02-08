<?php

$addon = rex_addon::get('googleplaces');
echo rex_view::title(rex_i18n::msg('googleplaces_title'));

if (rex_request('sync', 'int', null) === 1) {
    echo rex_view::success($addon->i18n('googleplaces_sync_success'));
}

// Wenn kein API-Schlüssel hinterlegt ist, dann Hinweis ausgeben
if (empty($addon->getConfig('api_key'))) {
    echo rex_view::warning(rex_i18n::msg('googleplaces_no_api_key'));
}

$table_name = 'rex_googleplaces_review';

rex_extension::register(
    'YFORM_MANAGER_DATA_PAGE_HEADER',
    static function (rex_extension_point $ep) {
        $yform = $ep->getParam('yform');
        $table = $yform->table;
        /** @var rex_yform_manager_table $table */
        if ($table->getTableName() === $ep->getParam('table_name')) {
            return '';
        }
    },
    rex_extension::EARLY,
    ['table_name' => $table_name],
);

$yform = $addon->getProperty('yform', []);
/** @var rex_yform_manager $yform */
$yform = $yform[rex_be_controller::getCurrentPage()] ?? [];

$_REQUEST['table_name'] = $table_name; /** @phpstan-ignore-line */

include rex_path::plugin('yform', 'manager', 'pages/data_edit.php');
