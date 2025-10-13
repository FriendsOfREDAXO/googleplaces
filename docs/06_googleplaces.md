# Klasse `GooglePlaces` für API-Zugriffe

Die Klasse `GooglePlaces` bietet statische Methoden für den Zugriff auf die Google Places API und für die Synchronisation von Places und Reviews.

## Namespace

```php
use FriendsOfRedaxo\GooglePlaces\GooglePlaces;
```

## Methoden und Beispiele

### `googleApiResult(string $place_id = null)`

Ruft die Details zu einem Google Place direkt über die Google Places API ab und gibt das Ergebnis als Array zurück.

**Parameter:**
- `$place_id` (optional): Die Google Place ID. Falls nicht angegeben, wird die Place ID aus der Addon-Konfiguration verwendet.

**Rückgabe:** Array mit den API-Daten oder ein leeres Array bei Fehlern

**Beispiel:**

```php
use FriendsOfRedaxo\GooglePlaces\GooglePlaces;

$placeData = GooglePlaces::googleApiResult('ChIJN1t_tDeuEmsRUsoyG83frY4');
if (!empty($placeData)) {
    echo "Name: " . $placeData['name'];
    echo "Adresse: " . $placeData['formatted_address'];
    echo "Bewertung: " . $placeData['rating'];
}
```

**API-Dokumentation:** [Google Places API Details](https://developers.google.com/maps/documentation/places/web-service/details?hl=de)

### `getFromGoogle(string $place_id = null, string $key = null)`

Ruft Details zu einem Google Place direkt über die Google Places API ab.

**Parameter:**
- `$place_id` (optional): Die Google Place ID. Falls nicht angegeben, wird die Place ID aus der Addon-Konfiguration verwendet.
- `$key` (optional): Wenn angegeben, wird nur der Wert für diesen Schlüssel aus dem API-Ergebnis zurückgegeben.

**Rückgabe:** Array oder String (abhängig vom `$key`-Parameter)

**Beispiel ohne Key:**

```php
$placeData = GooglePlaces::getFromGoogle('ChIJN1t_tDeuEmsRUsoyG83frY4');
// Gibt das vollständige Array zurück
```

**Beispiel mit Key:**

```php
$placeName = GooglePlaces::getFromGoogle('ChIJN1t_tDeuEmsRUsoyG83frY4', 'name');
// Gibt nur den Namen zurück
```

### `getPlaceDetails(string $place_id = null)`

Ruft Details zu einem Google Place ab. Prüft zuerst, ob die Daten in der lokalen Datenbank vorhanden sind. Falls nicht, werden sie über die API abgerufen.

**Parameter:**
- `$place_id` (optional): Die Google Place ID. Falls nicht angegeben, wird die Place ID aus der Addon-Konfiguration verwendet.

**Rückgabe:** Array mit den Place-Details oder `false` bei Fehler

**Beispiel:**

```php
$placeDetails = GooglePlaces::getPlaceDetails('ChIJN1t_tDeuEmsRUsoyG83frY4');
if ($placeDetails !== false) {
    echo "Name: " . $placeDetails['name'];
}
```

### `getAllReviewsLive(string $place_id = null)`

Ruft die letzten Reviews (in der Regel die letzten 5) zu einem Google Place direkt über die Google Places API ab.

**Parameter:**
- `$place_id` (optional): Die Google Place ID. Falls nicht angegeben, wird die Place ID aus der Addon-Konfiguration verwendet.

**Rückgabe:** Array mit Reviews oder ein leeres Array

**Beispiel:**

```php
$reviews = GooglePlaces::getAllReviewsLive('ChIJN1t_tDeuEmsRUsoyG83frY4');
foreach ($reviews as $review) {
    echo "Autor: " . $review['author_name'];
    echo "Bewertung: " . $review['rating'];
    echo "Text: " . $review['text'];
}
```

> **Hinweis:** Die Google Places API liefert standardmäßig maximal die 5 neuesten Reviews zurück.

### `syncAll()`

Synchronisiert alle in der Datenbank gespeicherten Places mit den aktuellen Daten aus der Google Places API. Dabei werden auch die Reviews synchronisiert, sofern dies in den Einstellungen aktiviert ist.

**Rückgabe:** `true` bei Erfolg (mindestens ein Place erfolgreich synchronisiert), `false` bei Fehler

**Beispiel:**

```php
$success = GooglePlaces::syncAll();
if ($success) {
    echo "Synchronisation erfolgreich abgeschlossen";
} else {
    echo "Fehler bei der Synchronisation";
}
```

> **Tipp:** Diese Methode wird auch vom Cronjob verwendet, um die automatische Synchronisation durchzuführen.

## Fehlerbehandlung

Die Methoden der `GooglePlaces`-Klasse protokollieren Fehler über das REDAXO-Logging-System. Fehler können im System-Log eingesehen werden.

Mögliche Fehlerquellen:
- Fehlender oder ungültiger API-Schlüssel
- Ungültige Place ID
- API-Limitierung erreicht
- Netzwerkfehler

**Beispiel für Fehlerprüfung:**

```php
$placeData = GooglePlaces::googleApiResult('ChIJN1t_tDeuEmsRUsoyG83frY4');

// Prüfen auf API-Fehler
if (isset($placeData['error'])) {
    echo "API-Fehler: " . $placeData['error'];
    // Fehler wird auch im System-Log protokolliert
}
```
