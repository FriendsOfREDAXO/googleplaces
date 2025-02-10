# Google Places Add-on für REDAXO 5

Das Add-on Google Places für REDAXO 5 ermöglicht die Nutzung der Google Places API. Es ist möglich, Informationen wie bspw. Bewertungen (Reviews), Geodaten, Bilder, Öffnungszeiten, etc. zu einem Eintrag in Google Maps via API-Aufruf zu erhalten und Reviews in der eigenen Datenbank zu speichern und auszugeben.

![splashscreen](https://raw.githubusercontent.com/FriendsOfREDAXO/googleplaces/refs/heads/main/assets/img/splashscreen.jpg)

## Neu in Version 3.X

* 💯 Verwalte **beliebig viele Places**
* ⭐ Speichere **Reviews** für mehrere Places
* 🔄️ **Manuelle Synchronisation** aus dem Backend heraus
* 🔄️ **Automatische Synchronisation** mit vorinstalliertem Cronjob
* 🏪 Neue **Backend-Ansicht für Places**
* 💁🏻 **Überarbeitete Dokumentation** mit Beispielen zu den neuen Methoden
* 🔎 **Suche direkt im Backend** nach Google Places-Einträgen
* 🦖 Umstellung auf **FriendsOfREDAXO-Namespace**
* ✅ **YOrm**-basierte Klassen für Places und Reviews
* ➡️ **YForm**-Tableset für Places und Reviews
* 5️⃣ **Bootstrap 5** Modul-Beispiel

> **Hinweis:** Diese Dokumentation wurde noch nicht vollständig aktualisiert. Einige Informationen können veraltet sein.

## Voraussetzungen

### Google Places API-Key

Das Add-on benötigt einen gültigen API-Key. Der Key muss die Places-API zulassen (In jedem Fall die Einschränkung des API-Keys auf bestimmte Domains oder IP-Adressen berücksichtigen, damit der Key nicht unbefugt benutzt werden kann). Auf dieser Seite wird beschrieben, wie man einen API-Key generiert:

<https://developers.google.com/maps/documentation/places/web-service/get-api-key>

### Google Places-Einträge

Damit man eine Location eindeutig identifizieren kann, benötigt man die ID. Über diesen Link kann man die ID herausfinden:

<https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder?hl=de>

> **Neu in 3.0.0:** Über die Backend-Seite `Google Places >  Eintrag suchen` kannst du direkt eine Suche nach Einträgen durchführen und Einträge in deine Datenbank hinzufügen.

## Was wird abgerufen?

### Der Google-Places-Eintrag

Der Google-Places-Eintrag enthält alle Informationen zu einem Ort und wird als JSON in der Tabelle `rex_googleplaces_place_detail` abgespeichert. 

Erstelle einen neuen Eintrag und rufe anschließend die Daten über "Jetzt aktualisieren" ab, oder regelmäßig via Cronjob.

> **Neu in 3.0.0:** Über die Backend-Seite `Google Places >  Eintrag suchen` kannst du direkt eine Suche nach Einträgen durchführen und Einträge in deine Datenbank hinzufügen.

Die Google Places API beschränkt den Zugriff auf die letzten 5 Rezensionen.

### Rezensionen (Reviews) zu einem Google-Places-Eintrag ausgeben

#### Beispiele für die Ausgabe als Fragment und Modul 

Details zum Google Places-Eintrag und die Rezensionen (Reviews) können per `rex_sql` oder mittels `YOrm` geholt und individuell ausgegeben werden.

Dieses Beispiel-Modul nutzt das MForm-Addon für die Auswahl eines Google-Places-Eintrags aus der Datenbank und gibt diese über das mitglieferte Fragment aus. 

Die entsprechende CSS-Datei mit den Styles für die Ausgabe liegt im `assets`-Ordner des Add-ons unter: `/assets/addons/googleplaces/css/reviews.css`

> **Tipp:** Das Fragment kann bspw. auch im Template ausgegeben werden. 

#### Modul-Eingabe mit MForm

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
#### Modul-Ausgabe

```php
<?php
use FriendsOfRedaxo\GooglePlaces\Place;

$fragment = new rex_fragment();
$id = (int)"REX_VALUE[1]" ?: 1;
$fragment->setVar('place', Place::get($id), false); // Ersetze 1 durch die ID des gewünschten Eintrags
echo $fragment->parse('googleplaces/reviews.bs5.php');
```

### Cronjob: Google Places-Eintrag Rezensionen regelmäßig automatisch abrufen

Bei der Installation des Add-ons wird der Cronjob `Google Places: Reviews per API-Call aktualisieren` installiert und aktiviert. Du kannst den Eintrag jederzeit deaktivieren, wenn du keine automatische Aktualisierung möchtest.

## Autor

**Friends Of REDAXO**

* <http://www.redaxo.org>
* <https://github.com/FriendsOfREDAXO>

**Lead**

[Alexander Walther](https://github.com/alxndr-w)

**Ursprünglich entwickelt von**

[Daniel Springer](https://github.com/danspringer)
