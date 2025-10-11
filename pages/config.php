<?php

$addon = rex_addon::get('googleplaces');
echo rex_view::title(rex_i18n::msg('googleplaces_title'));

$form = rex_config_form::factory($addon->getName());

$field = $form->addInputField('text', 'api_key', null, ['class' => 'form-control']);
$field->setLabel('Google Maps API-Key');

$field = $form->addSelectField('auto_publish_reviews', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('googleplaces_config_auto_publish_reviews'));
$field->setNotice(rex_i18n::msg('googleplaces_config_auto_publish_reviews_notice'));
$select = $field->getSelect();
$select->addOption(rex_i18n::msg('googleplaces_config_auto_publish_reviews_no'), '0');
$select->addOption(rex_i18n::msg('googleplaces_config_auto_publish_reviews_yes'), '1');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', 'Grundeinstellungen', false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');
