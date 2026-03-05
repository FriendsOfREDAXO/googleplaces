<?php

use FriendsOfRedaxo\GooglePlaces\Review;

$addon = rex_addon::get('googleplaces');
echo rex_view::title(rex_i18n::msg('googleplaces_title'));

// Status-Toggle verarbeiten
$func = rex_request('func', 'string', '');
if ($func === 'changestatus') {
    $reviewId = rex_request('review_id', 'int', 0);
    if ($reviewId > 0) {
        $review = Review::get($reviewId);
        if ($review) {
            $newStatus = $review->getStatus() === Review::STATUS_VISIBLE
                ? Review::STATUS_HIDDEN
                : Review::STATUS_VISIBLE;
            $review->setStatus($newStatus);
            $review->save();
        }
    }
}

if (rex_request('sync', 'int', null) === 1) {
    $syncErrors = rex_request('sync_errors', 'int', 0);
    if ($syncErrors > 0) {
        echo rex_view::warning($addon->i18n('googleplaces_sync_success') . ' ' . $addon->i18n('googleplaces_sync_errors', $syncErrors));
    } else {
        echo rex_view::success($addon->i18n('googleplaces_sync_success'));
    }
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

if (version_compare(rex_addon::get('yform')->getVersion(), '5.0.0', '<')) {
    include rex_path::plugin('yform', 'manager', 'pages/data_edit.php');
} else {
    include rex_path::addon('yform', 'pages/manager.data_edit.php');
}
