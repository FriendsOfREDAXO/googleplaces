# Rezensionen (Reviews) zu einem Google-Places-Eintrag ausgeben

## Beispiele für die Ausgabe als Fragment und Modul

Details zum Google Places-Eintrag und die Rezensionen (Reviews) können per `rex_sql` oder mittels `YOrm` geholt und individuell ausgegeben werden.

Dieses Beispiel-Modul nutzt das MForm-Addon für die Auswahl eines Google-Places-Eintrags aus der Datenbank und gibt diese über das mitglieferte Fragment aus.

Die entsprechende CSS-Datei mit den Styles für die Ausgabe liegt im `assets`-Ordner des Add-ons unter: `/assets/addons/googleplaces/css/reviews.css`

> **Tipp:** Das Fragment kann bspw. auch im Template ausgegeben werden.

### Modul-Eingabe mit MForm

```php
<?php
use FriendsOfRedaxo\MForm;
use FriendsOfRedaxo\GooglePlaces\Place;

$mform = MForm::factory();
$mform->addFieldsetArea('Select elements');

$places = Place::query()->find();
$options = [];
foreach($places as $place) {
    $options[$place->getId()] = $place->getName();   
}

$mform->addSelectField("1.0", $options, ['label' => 'Standort auswählen']);

echo $mform->show();
```

### Modul-Ausgabe

```php
<?php
use FriendsOfRedaxo\GooglePlaces\Place;

$fragment = new rex_fragment();
$id = (int)"REX_VALUE[1]" ?: 1;
$fragment->setVar('place', Place::get($id), false); // Ersetze 1 durch die ID des gewünschten Eintrags
echo $fragment->parse('googleplaces/reviews.bs5.php');
```
