<?php

use FriendsOfRedaxo\GooglePlaces\Place;

$addon = rex_addon::get('googleplaces');
echo rex_view::title(rex_i18n::msg('googleplaces_title'));

// Wenn kein API-Schlüssel hinterlegt ist, dann Hinweis ausgeben
if (empty($addon->getConfig('api_key'))) {
    echo rex_view::warning(rex_i18n::msg('googleplaces_no_api_key'));
}

// Wenn ein Place hinzugefügt werden soll
if (rex_get('place_id')) {
    $placeId = rex_get('place_id');
    $place = Place::query()->where('place_id', $placeId)->findOne();
    if ($place) {
        echo rex_view::warning(rex_i18n::msg('googleplaces_query_place_already_added'));
    } else {
        $place = Place::create();
        $place->setPlaceId($placeId);
        $place->save();
        echo rex_view::success(rex_i18n::msg('googleplaces_query_place_added'));
    }
}

// Notice
$notice =  rex_view::info(rex_i18n::msg('googleplaces_query_info'));

// Formular mit einem Suchfeld

$form = '<form action="' . rex_url::currentBackendPage() . '" method="post">
<div class="row">
    <div class="col-12 col-md-3">
        <div class="form-group">
            <label for="name">' . rex_i18n::msg('googleplaces_query_name') . '</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="' . rex_i18n::msg('googleplaces_query_name_placeholder') . '" value="' . rex_escape(rex_request('name')) . '">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group">
                <label for="street">' . rex_i18n::msg('googleplaces_query_street') . '</label>
                <input type="text" class="form-control" id="street" name="street" placeholder="' . rex_i18n::msg('googleplaces_query_street_placeholder') . '" value="' . rex_escape(rex_request('street')) . '">
                </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group">
                <label for="zip">' . rex_i18n::msg('googleplaces_query_zip') . '</label>
                <input type="text" class="form-control" id="zip" name="zip" placeholder="' . rex_i18n::msg('googleplaces_query_zip_placeholder') . '" value="' . rex_escape(rex_request('zip')) . '">
                </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group">
                <label for="city">' . rex_i18n::msg('googleplaces_query_city') . '</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="' . rex_i18n::msg('googleplaces_query_city_placeholder') . '" value="' . rex_escape(rex_request('city')) . '">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">' . rex_i18n::msg('googleplaces_query_submit') . '</button>
</form>';


$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('googleplaces_query_title'));
$fragment->setVar('body', $notice.$form, false);
echo $fragment->parse('core/page/section.php');

if (rex_request('name') || rex_request('street') || rex_request('zip') || rex_request('city')) {
    $name = rex_request('name', 'string', '');
    $street = rex_request('street', 'string', '');
    $zip = rex_request('zip', 'string', '');
    $city = rex_request('city', 'string', '');

    // API-Key aus den Addon-Einstellungen auslesen
    $apiKey = $addon->getConfig('api_key');

    // Suchanfrage an die Google Places API senden
    $result = rex_api_find_place_id::queryPlaces($name, $street, $zip, $city, $apiKey);

    if (isset($result['error'])) {
        echo rex_view::error($result['error']);
    }
    if (isset($result['places']) && count($result['places']) === 0) {
        echo rex_view::warning(rex_i18n::msg('googleplaces_query_no_results'));
    }
    if (isset($result['places']) && count($result['places']) > 0) {
        $table = '';
        $table .= '<table class="table table-striped">
        <thead>
            <tr>
                <th>' . rex_i18n::msg('googleplaces_query_place_id') . '</th>
                <th>' . rex_i18n::msg('googleplaces_query_table_name') . '</th>
                <th>' . rex_i18n::msg('googleplaces_query_action') . '</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($result['places'] as $place) {
            $table .= '<tr>
            <td>' . $place['id'] . '</td>
            <td><strong>' . $place['displayName']['text'] . '</strong><br>' . $place['formattedAddress'] . '</td>
            <td><a href="' . rex_url::currentBackendPage(['place_id' => $place['id'], 'name' => $name, 'street' => $street, 'zip' => $zip, 'city' => $city ]) . '" class="btn btn-primary">' . rex_i18n::msg('googleplaces_query_add') . '</a></td>
        </tr>';
        }
        $table .= '</tbody>
    </table>';
        
        $fragment = new rex_fragment();
        $fragment->setVar('title', rex_i18n::msg('googleplaces_query_results'));
        $fragment->setVar('body', $table, false);
        echo $fragment->parse('core/page/section.php');
    }
}
