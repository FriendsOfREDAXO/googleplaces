<?php

$addon = rex_addon::get('googleplaces');
echo rex_view::title(rex_i18n::msg('googleplaces_title'));

$form = rex_config_form::factory($addon->getName());

$field = $form->addInputField('text', 'gmaps-api-key', null, ['class' => 'form-control']);
$field->setLabel('Google Maps API-Key');


$field = $form->addInputField('text', 'gmaps-location-id', null, ['class' => 'form-control']);
$field->setLabel('Google Places Location ID');
$field->setNotice('Deprecated: Dieses Konfigurationsfeld wird zukünftig nicht mehr benötigt. Verwalte stattdessen ein oder mehrere Places über die Tabelle "Places".');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', 'Grundeinstellungen', false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');
