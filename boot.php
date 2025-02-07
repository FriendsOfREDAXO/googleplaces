<?php

use FriendsOfRedaxo\GooglePlaces\Place;
use FriendsOfRedaxo\GooglePlaces\Review;

\rex_cronjob_manager::registerType('FriendsOfRedaxo\GooglePlaces\Cronjob');


if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
	rex_yform_manager_dataset::setModelClass(
		'rex_googleplaces_place_detail',
		Place::class, // Hier anpassen, falls Namespace verwendet wird
	);

	rex_yform_manager_dataset::setModelClass(
		'rex_googleplaces_review',
		Review::class, // Hier anpassen, falls Namespace verwendet wird
	);
}

if (in_array(rex_be_controller::getCurrentPagePart(1), ['yform', 'googleplaces'], true)) {
	rex_extension::register('YFORM_DATA_LIST', Place::epYformDataList(...));
	rex_extension::register('YFORM_DATA_LIST', Review::epYformDataList(...));

}
