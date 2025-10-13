# Datenbank-Struktur

Das Addon nutzt zwei Haupttabellen für die Speicherung von Google Places-Daten und Reviews.

## Tabelle: `rex_googleplaces_place_detail`

Speichert die Details zu Google Places-Einträgen.

### Felder

| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | int | Primärschlüssel (auto_increment) |
| `place_id` | varchar(191) | Google Place ID (eindeutig) |
| `api_response_json` | text | Vollständige API-Antwort als JSON |
| `createdate` | datetime | Erstellungsdatum des Eintrags |
| `updatedate` | datetime | Letztes Update durch Synchronisation |
| `review_ids` | varchar(191) | Relation zu Reviews (YForm-Relation) |

### Indizes

- **PRIMARY**: `id`
- **UNIQUE**: `place_id`

### YForm-Modell

```php
use FriendsOfRedaxo\GooglePlaces\Place;

// Zugriff auf Tabelle über YOrm
$place = Place::get($id);
$places = Place::query()->find();
```

### Beispiel für gespeicherte API-Antwort

Das Feld `api_response_json` enthält die vollständige Antwort der Google Places API:

```json
{
  "name": "Restaurant Beispiel",
  "formatted_address": "Hauptstraße 1, 12345 Berlin, Deutschland",
  "formatted_phone_number": "+49 30 12345678",
  "rating": 4.5,
  "user_ratings_total": 127,
  "url": "https://maps.google.com/?cid=123456789",
  "photos": [...],
  "reviews": [...]
}
```

## Tabelle: `rex_googleplaces_review`

Speichert die Bewertungen (Reviews) zu Google Places-Einträgen.

### Felder

| Feldname | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | int | Primärschlüssel (auto_increment) |
| `place_detail_id` | int | Fremdschlüssel zu `rex_googleplaces_place_detail` |
| `google_place_id` | varchar(191) | Google Place ID des zugehörigen Ortes |
| `author_name` | varchar(191) | Name des Autors/der Autorin |
| `rating` | int | Bewertung (1-5 Sterne) |
| `author_url` | varchar(191) | URL zum Google-Profil des Autors |
| `language` | varchar(191) | Sprache der Bewertung (z.B. "de", "en") |
| `text` | text | Bewertungstext |
| `profile_photo_url` | varchar(191) | URL zum Profilbild bei Google |
| `profile_photo_base64` | text | **Veraltet (ab 3.1)**: Base64-kodiertes Profilbild |
| `profile_photo_file` | varchar(191) | **Neu ab 3.1**: Dateiname des Profilbilds im Dateisystem |
| `createdate` | datetime | Erstellungsdatum des Eintrags in der DB |
| `status` | tinyint(1) | Sichtbarkeit: 1 = sichtbar, 0 = ausgeblendet |
| `publishdate` | datetime | Veröffentlichungsdatum der Bewertung bei Google |
| `updatedate` | datetime | Letztes Update des Eintrags |
| `uuid` | varchar(36) | Eindeutiger Identifikator (UUID v4) |
| `time` | varchar(191) | **Veraltet**: Unix-Timestamp (wird zu `publishdate` konvertiert) |

### Indizes

- **PRIMARY**: `id`
- **UNIQUE**: `uuid`
- **UNIQUE**: `google_place_id`, `author_url` (kombiniert)

### YForm-Modell

```php
use FriendsOfRedaxo\GooglePlaces\Review;

// Zugriff auf Tabelle über YOrm
$review = Review::get($id);
$reviews = Review::query()->find();
```

### Relationen

**Review → Place:**
```php
$review = Review::get($id);
$place = $review->getPlace(); // Gibt das zugehörige Place-Objekt zurück
```

**Place → Reviews:**
```php
$place = Place::get($id);
$reviews = $place->getReviews(); // Gibt Collection von Review-Objekten zurück
```

## Profilbilder-Speicherung (ab Version 3.1)

Ab Version 3.1 werden Profilbilder im Dateisystem gespeichert statt als Base64-String in der Datenbank.

### Speicherort

```
redaxo/data/addons/googleplaces/profile_photos/
```

### Dateiname

Der Dateiname entspricht der UUID des Reviews mit `.jpg`-Endung:

```
a1b2c3d4-e5f6-7890-abcd-ef1234567890.jpg
```

### Rückwärtskompatibilität

Bestehende Base64-Profilbilder bleiben in der Datenbank erhalten. Die Methode `getProfilePhotoSrc()` gibt automatisch die richtige Quelle zurück:

1. Prüft zuerst, ob eine Datei im Dateisystem existiert
2. Falls nicht, gibt sie den Base64-String zurück
3. Falls auch dieser nicht vorhanden ist, gibt sie `null` zurück

## UUID-Generierung

Die UUID für Reviews wird generiert aus:
- Google Place ID
- Author URL (Google-Profillink)

Dies stellt sicher, dass dieselbe Bewertung nicht mehrfach gespeichert wird.

```php
use rex_yform_value_uuid;

$uuid = rex_yform_value_uuid::guidv4(md5($place_id . $author_url));
```

## Synchronisation

Bei der Synchronisation (`Place::sync()` oder `GooglePlaces::syncAll()`):

1. **Place-Details** werden immer aktualisiert
2. **Reviews** werden nur synchronisiert, wenn in den Einstellungen aktiviert
3. **Neue Reviews** erhalten automatisch einen Status (abhängig von Auto-Publish-Einstellung)
4. **Bestehende Reviews** werden aktualisiert, aber nicht überschrieben

### Konfliktauflösung

Die Tabelle nutzt UNIQUE-Constraints, um Duplikate zu vermeiden:
- Ein Place kann nur einmal mit derselben `place_id` existieren
- Ein Review kann nur einmal mit derselben Kombination aus `google_place_id` und `author_url` existieren

## Migration von Version 2.X

Bei der Installation werden alte Tabellen automatisch umbenannt:
- `mf_googleplaces_reviews` → `rex_googleplaces_review`
- `mf_googleplaces_place_details` → `rex_googleplaces_place_detail`

Das Feld `time` (Unix-Timestamp) wird zu `publishdate` (Datetime) konvertiert.
