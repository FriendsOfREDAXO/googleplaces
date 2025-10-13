# Backend-Seiten und Einstellungen

Das Addon stellt mehrere Backend-Seiten zur Verwaltung von Google Places und Reviews zur Verfügung.

## Übersicht der Backend-Seiten

### Abfrage (Query)

**Pfad:** `Google Places → Abfrage`  
**Datei:** `pages/query.php`

Auf dieser Seite können neue Google Places zur Datenbank hinzugefügt werden:

1. **Suchformular**: Eingabe von Name, Straße, PLZ und Stadt
2. **Suchergebnisse**: Anzeige der gefundenen Places von Google
3. **Hinzufügen**: Places können durch Klick zur Datenbank hinzugefügt werden

**API-Endpunkt:** Die Suche nutzt die neue Google Places Text Search API über den Endpunkt `rex_api_find_place_id`.

### Places (Place Details)

**Pfad:** `Google Places → Places`  
**Datei:** `pages/place_detail.php`

Zeigt eine Liste aller in der Datenbank gespeicherten Google Places mit:
- Place ID
- Name, Adresse und Telefonnummer (aus der API-Antwort)
- Anzahl der Fotos
- Durchschnittliche Bewertung und Anzahl der Bewertungen
- Letztes Update-Datum

Die Ansicht nutzt YForm-Tablesets für die Verwaltung der Einträge.

### Reviews (Rezensionen)

**Pfad:** `Google Places → Reviews`  
**Datei:** `pages/review.php`

Zeigt eine Liste aller in der Datenbank gespeicherten Reviews mit:
- Zugehöriger Place (mit Link zur Place-Detailseite)
- Autor*in mit Profilbild
- Bewertung (Sterne)
- Sprache
- Veröffentlichungsdatum
- Status (sichtbar/ausgeblendet)

Die Ansicht nutzt YForm-Tablesets für die Verwaltung der Einträge.

**Status-Verwaltung:** Reviews können manuell auf "sichtbar" oder "ausgeblendet" gesetzt werden.

### Synchronisieren

**Pfad:** `Google Places → Synchronisieren`  
**Datei:** `pages/sync.php`

Führt eine manuelle Synchronisation aller Places durch:
1. Ruft für jeden Place die aktuellen Daten von der Google Places API ab
2. Aktualisiert die Place-Details in der Datenbank
3. Synchronisiert die Reviews (falls aktiviert)
4. Zeigt eine Erfolgsmeldung oder Fehlermeldung an

Nach der Synchronisation erfolgt eine Weiterleitung zur Reviews-Seite mit Status-Information.

### Einstellungen (Config)

**Pfad:** `Google Places → Einstellungen`  
**Datei:** `pages/config.php`

Konfigurationsseite für das Addon mit folgenden Einstellungen:

#### Google Maps API-Key

Eingabefeld für den Google Maps API-Schlüssel. Dieser ist erforderlich für:
- Abrufen von Place-Details
- Synchronisation von Reviews
- Suche nach Places

**So erhältst du einen API-Schlüssel:**
1. Besuche die [Google Cloud Console](https://console.cloud.google.com/)
2. Erstelle ein Projekt oder wähle ein bestehendes aus
3. Aktiviere die "Places API"
4. Erstelle einen API-Schlüssel unter "Anmeldedaten"

#### Review-Synchronisation

**Optionen:**
- **Ja**: Reviews werden bei der Synchronisation mit abgerufen und gespeichert
- **Nein**: Nur Place-Details werden synchronisiert, Reviews bleiben unberührt

**Standard:** Ja

**Anwendungsfall für "Nein":**
- API-Kontingent schonen
- Reviews werden manuell gepflegt
- Keine Reviews gewünscht

#### Auto-Publish für neue Reviews

**Optionen:**
- **Sichtbar**: Neue Reviews werden automatisch mit Status "sichtbar" angelegt
- **Ausgeblendet**: Neue Reviews müssen manuell freigeschaltet werden

**Standard:** Ausgeblendet

**Empfehlung:** "Ausgeblendet" verwenden, um Reviews vor der Veröffentlichung prüfen zu können.

### Dokumentation (Docs)

**Pfad:** `Google Places → Dokumentation`  
**Datei:** `pages/docs.php`

Zeigt die Markdown-Dokumentation aus dem `docs/`-Ordner an:
- Automatische Generierung des Inhaltsverzeichnisses
- Syntax-Highlighting für Code-Beispiele
- Navigation zwischen den Dokumentationsseiten

Die Dokumentation umfasst:
- `01_intro.md`: Einführung und Changelog
- `02_upgrade.md`: Upgrade-Anleitung
- `03_place.md`: Place-Klasse
- `04_review.md`: Review-Klasse
- `05_module.md`: Modul-Beispiele
- `06_googleplaces.md`: GooglePlaces-Klasse
- `07_cronjob.md`: Cronjob-Dokumentation
- `08_backend.md`: Backend-Seiten (diese Datei)

## YForm-Integration

Die Verwaltung von Places und Reviews erfolgt über YForm-Tablesets. Dies ermöglicht:

- **Flexible Datenverwaltung**: Standard-CRUD-Operationen
- **Suchfunktion**: Volltextsuche in Places und Reviews
- **Export/Import**: Daten können exportiert und importiert werden
- **Massenbearbeitung**: Mehrere Einträge gleichzeitig bearbeiten
- **Massenarchivierung**: Mehrere Einträge gleichzeitig löschen

## API-Endpunkte

### rex_api_find_place_id

**Datei:** `lib/Api/FindPlaceId.php`  
**URL:** `index.php?rex-api-call=find_place_id`

**Parameter:**
- `name`: Name des Ortes
- `street`: Straße
- `zip`: Postleitzahl
- `city`: Stadt

**Rückgabe:** JSON-Array mit gefundenen Places

**Beispiel:**

```
GET /index.php?rex-api-call=find_place_id&name=Restaurant&street=Hauptstraße 1&zip=12345&city=Berlin
```

**Response:**

```json
{
  "ChIJN1t_tDeuEmsRUsoyG83frY4": {
    "text": "Restaurant Beispiel",
    "formattedAddress": "Hauptstraße 1, 12345 Berlin, Deutschland"
  }
}
```

Dieser Endpunkt nutzt die neue Google Places Text Search API (v1).
