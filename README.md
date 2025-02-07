# Google Places Add-on für REDAXO 5

Das Add-on Google Places für REDAXO 5 ermöglicht die Nutzung der Google Places API. Es ist möglich, Informationen wie bspw. Bewertungen (Reviews), Geodaten, Bilder, Öffnungszeiten, etc. zu einem Eintrag in Google Maps via API-Aufruf zu erhalten und Reviews in der eigenen Datenbank zu speichern und auszugeben.

![splashscreen](https://user-images.githubusercontent.com/16903055/140534021-cd09791c-9dc5-4c11-8f40-d16e72b43cf8.jpg)

## Neu in Version 3.X

* 💯 Verwalte **beliebig viele Places**
* ⭐ Speichere **Reviews** für mehrere Places
* 🔄️ **Manuelle Synchronisation** aus dem Backend heraus
* 5️⃣ **Bootstrap 5** Modul-Beispiel
* 🦖 Umstellung auf **FriendsOfREDAXO-Namespace**
* ✅ **YOrm**-basierte Klassen für Places und Reviews
* ➡️ **YForm**-Tableset für Places und Reviews
* 🏪 Neue **Backend-Ansicht für Places**
* 💁🏻 **Überarbeitete Dokumentation** mit Beispielen zu den neuen Methoden

## Voraussetzungen

### Google Places API-Key

Das Add-on benötigt einen gültigen API-Key. Der Key muss die Places-API zulassen (In jedem Fall die Einschränkung des API-Keys auf bestimmte Domains oder IP-Adressen berücksichtigen, damit der Key nicht unbefugt benutzt werden kann). Auf dieser Seite wird beschrieben, wie man einen API-Key generiert:

<https://developers.google.com/maps/documentation/places/web-service/get-api-key>

### Google Places ID

Damit man eine Location eindeutig identifizieren kann, benötigt man die ID. Über diesen Link kann man die ID herausfinden: <https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder?hl=de>

**Gültiger API-Key und Place-ID müssen in die Konfiguration des Add-ons eingetragen werden.**

## Google-Place-Informationen

### Infos zu einem Place

Mittels der Funktion `gplace::get()` ist es möglich, direkt über die Google-API Informationen zum PLace in Google Maps zu erhalten.

### Einzelne Attribute zum Place

Einzelne Werte oder Arrays kann man wie folgt ansprechen:

* `gplace::get('name_des_wertes')`

Entsprechend beispielhaft:

* Öffnungszeiten (Array) bspw.: `gplace::get('opening_hours')`

* Maps-URL (string): `gplace::get('url')`

* Adresse (string): `gplace::get('formatted_address')`

Detailinfos zu den Google-Place-Attributen gibt es hier:

<https://developers.google.com/maps/documentation/places/web-service/details#Place>

## Google-Reviews

Das Add-on ermöglicht den direkten Aufruf über die Google-API, was bei jedem Aufruf über den im Add-on hinterlegten API-Key bei Google abgerechnet wird.

Außerdem kann man Reviews automatisch in einer eigenen Tabelle speichern und so Googles-API-Beschränkungen umgehen bzw. die Anzahl der API-Calls reduzieren.

### "Live"-Aufruf der Reviews über die Google-API

* `gplace::getAllReviewsFromGoogle()`

Ruft Reviews zum Google Place direkt über die Google API ab (wsl. limitiert auf die letzten 5 Reviews). Pro Aufruf wird hier von Google ein API-Call registriert und abgerechnet.

### Review-Aufrufe über die eigene Datenbank

Weiterhin ist es möglich die Reviews zu einem Google Place in einer eigenen REDAXO-Datenbank zu speichern und so häufige Aufrufe der Google-API zu vermeiden. Dies hat den Vorteil, dass die Beschränkung auf 5 Reviews bei einem "live" -API-Aufruf von Google mit der Zeit umgangen werden kann, da Reviews automatisch in der eigenen Datenbank via Cronjob gespeichert werden können.

Die Reviews befinden sich in der Tabelle `rex_googleplaces_review`. Entweder greift man selbst per SQL darauf zu oder nutzt die vom Add-on mitgelieferten Funktionen:

* `gplace::getAllReviews()`
*
  Ruft alle Reviews aus der eigenen DB ab und gibt ein Array zurück.

### Reviews automatisch via Cronjob in Datenbanktabelle speichern

Bei der Installation des Add-ons wurde eine Tabelle mit dem Namen `rex_googleplaces_review` angelegt. Außerdem steht im Cronjob-Add-on der Cronjob-Typ `Google Places: Reviews per API-Call aktualisieren` zur Verfügung.

Der Cronjob ruft die Funktion `gplace::updateReviewsDB()` aus und speichert die letzten 5 Reviews, die als Antwort von Google kommen in der Tabelle. Anhand des Timestamps wird überprüft, ob der Review bereits in der Tabelle vorhanden ist oder nicht. Entsprechend wird er gespeichert oder übersprungen.

Auf diese Weise wächst die Menge an gespeicherten Reviews in der eigenen Datenbank mit der Zeit an und man kann das Google-Limit umgehen, welches nur die letzten 5 Reviews zurückgibt.

Außerdem spart man so deutlich an API-Aufrufen, wenn man den Cronjob bspw. auf einmal pro Tag  konfiguriert. Die API wird dann nur einmal pro Tag aufgerufen und nicht bei jeder Darstellung der Reviews.

## Modulausgabe

Grundsätzlich können die Reviews aus der Tabelle auch per SQL geholt werden und anschließend individuell ausgegeben und dargestellt werden.

Das Add-on bringt ein Beispiel-Modul für den Output mit. Hierzu wird bspw. das Fragment `googleplaces/reviews.bs4.php` verwendet. Das Fragment kann auch leicht bspw. über das Theme-Add-on oder das Project-Add-on updatesicher überschrieben werden.

Die Beispiel-Module holen die Reviews jeweils aus der eigenen Datenbank und nicht über die Google-API.

Die entsprechende CSS-Datei mit den Styles für die Ausgabe liegt im `assets`-Ordner des Add-ons.

### Beispiel-Modul mit Bootstrap 5 Markup

![BS5 Modul](..//assets/addons/googleplaces/img/bsp-modul-bs5.jpg)

Das Modul benötigt Bootstrap 4 und Font Awesome für die Sterne.

CSS-Style: `/assets/addons/googleplaces/css/reviews-bs5.css`

```php
<?php
$fragment = new rex_fragment();
$fragment->setVar('place', Place::get(1), false);
echo $fragment->parse('googleplaces/reviews.bs5.php');
?>
```

### Beispiel-Modul mit Bootstrap 4 Markup

![BS4 Modul](..//assets/addons/googleplaces/img/bsp-modul-bs4.jpg)

Das Modul benötigt Bootstrap 4 und Font Awesome für die Sterne.

CSS-Style: `/assets/addons/googleplaces/css/reviews-bs4.css`

```php
<?php
$fragment = new rex_fragment();
$fragment->setVar('place', Place::get(1), false);
echo $fragment->parse('googleplaces/reviews.bs4.php');
?>
```

### Beispiel-Modul mit Bootstrap 3 Markup

![BS3 Modul](..//assets/addons/googleplaces/img/bsp-modul-bs3.jpg)

Das Modul benötigt Bootstrap 3 und Font Awesome für die Sterne.

CSS-Style: `/assets/addons/googleplaces/css/reviews-bs3.css`

```php
<?php
$fragment = new rex_fragment();
$fragment->setVar('place', Place::get(1), false);
echo $fragment->parse('googleplaces/reviews.bs3.php');
?>
```

## Autor

**Friends Of REDAXO**

* <http://www.redaxo.org>
* <https://github.com/FriendsOfREDAXO>

**Lead**
[Alexander Walther](https://github.com/alxndr-w)

**Ursprünglich entwickelt von**
[Daniel Springer]([https://www.e-recht24.de](https://github.com/danspringer))
