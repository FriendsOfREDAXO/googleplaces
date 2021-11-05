<?php
$code = "";
$code .= "<?php" . PHP_EOL;
$code .= "	//Ein Array mit dem gesamten Ergebnis zurückbekommen:" . PHP_EOL;
$code .= "	dump( gplace::get() );" . PHP_EOL;
$code .= PHP_EOL;
$code .= "	//Den Namen des Place:" . PHP_EOL;
$code .= "	echo gplace::get('name');" . PHP_EOL;
$code .= PHP_EOL;
$code .= "	//Anzahl User Bewertungen:" . PHP_EOL;
$code .= "	echo gplace::get('user_rating_total');" . PHP_EOL;
$code .= PHP_EOL;
$code .= "?>";

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', 'Beispiel: Module Output', false); //translate
$fragment->setVar('body', rex_string::highlight($code), false);
echo $fragment->parse('core/page/section.php');


if (rex_addon::get('mf_googleplaces')->getConfig('gmaps-api-key') && rex_addon::get('mf_googleplaces')->getConfig('gmaps-location-id')) {
    echo '<h3>Array des konfigurierten Google Place</h3>
	<p>Die einzelnen Werte können wie oben beschrieben über <code>gplace::get(\'name_des_wertes\')</code> geholt werden.</p>
	';
    dump( gplace::get() );
}
?>