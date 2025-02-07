<?php
$code = "";
$code .= "<?php" . PHP_EOL;
$code .= "	//Ein Array mit dem gesamten Ergebnis aus der eigenen Datenbank zurückbekommen:" . PHP_EOL;
$code .= "	dump( gplace::getPlaceDetails() );" . PHP_EOL;
$code .= PHP_EOL;
$code .= "	//Den Namen des Place:" . PHP_EOL;
$code .= "	echo gplace::getPlaceDetails('name');" . PHP_EOL;
$code .= PHP_EOL;
$code .= "	//Anzahl User Bewertungen:" . PHP_EOL;
$code .= "	echo gplace::getPlaceDetails('user_rating_total');" . PHP_EOL;
$code .= PHP_EOL;
$code .= "	//Ein Array mit dem gesamten Ergebnis direkt über die Google-API (kostenpflichtig) zurückbekommen:" . PHP_EOL;
$code .= "	dump( gplace::get() );" . PHP_EOL;
$code .= PHP_EOL;
$code .= "?>";

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', 'Beispiel: Module Output', false); //translate
$fragment->setVar('body', rex_string::highlight($code), false);
echo $fragment->parse('core/page/section.php');


if (rex_addon::get('googleplaces')->getConfig('gmaps-api-key') && rex_addon::get('googleplaces')->getConfig('gmaps-location-id')) {
    echo '<h3>Array des konfigurierten Google Place</h3>
	<p>Die einzelnen Werte können wie oben beschrieben über <code>gplace::get(\'name_des_wertes\')</code> (kostenpflichtig über die Google-API) oder <code>gplace::getPlaceDetails(\'name_des_wertes\')</code> (gratis aus der eigenen DB) geholt werden.</p>
	';
    dump(gplace::getPlaceDetails());
}
