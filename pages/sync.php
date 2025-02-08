<?php

use FriendsOfRedaxo\GooglePlaces\Place;

$addon = rex_addon::get('googleplaces');
echo rex_view::title(rex_i18n::msg('googleplaces_title'));

// Wenn kein API-Schlüssel hinterlegt ist, dann Hinweis ausgeben
if (empty($addon->getConfig('api_key'))) {
    echo rex_view::warning(rex_i18n::msg('googleplaces_no_api_key'));
} else {

    $places = Place::query()->find();

    foreach ($places as $place) {
        /** @var Place $place */
        $place->sync();
    }

    rex_response::sendRedirect(rex_url::backendPage('googleplaces/review', ['sync' => '1'], false));
}
