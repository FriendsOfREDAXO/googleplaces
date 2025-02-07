<?php

use FriendsOfRedaxo\GooglePlaces\Place;

$addon = rex_addon::get('googleplaces');

$places = Place::query()->find();

foreach ($places as $place) {
    $place->sync();
}

rex_response::sendRedirect(rex_url::backendPage('googleplaces/review', ['sync' => '1'], false));
