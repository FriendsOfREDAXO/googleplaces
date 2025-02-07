<?php
$content = '';
$buttons = '';

// Einstellungen speichern
if (rex_post('formsubmit', 'string') == '1') {
    $this->setConfig(rex_post('baseconfig', [
        ['gmaps-api-key', 'string'],
        ['gmaps-location-id', 'string'],
        
    ]));
    
    echo rex_view::success('Einstellungen gespeichert');
}

$content .= '<fieldset>';



// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="mf-places-gmaps-api-key">Google Maps API-Key</label>';
$n['field'] = '<input class="form-control places-gmaps-api-key" type="text" id="mf-places-gmaps-api-key" name="baseconfig[gmaps-api-key]" value="' . $this->getConfig('gmaps-api-key') . '"/>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$formElements = [];
$n = [];
$n['label'] = '<label for="mf-places-location-id">Google Places Location ID</label>';
$n['field'] = '<input class="form-control places-location-id" type="text" id="mf-places-location-id" name="baseconfig[gmaps-location-id]" value="' . $this->getConfig('gmaps-location-id') . '"/>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');




$content .= '</fieldset>';

// Save-Button
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="Speichern">Speichern</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');
$buttons = '
<fieldset class="rex-form-action">
    ' . $buttons . '
</fieldset>
';

// Ausgabe Formular
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', 'Grundeinstellungen');
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$output = $fragment->parse('core/page/section.php');

$output = '
<form action="' . rex_url::currentBackendPage() . '" method="post">
<input type="hidden" name="formsubmit" value="1" />
    ' . $output . '
</form>
';

echo $output;
