# Cronjob für automatische Synchronisation

Das Addon stellt einen Cronjob zur Verfügung, der die automatische Synchronisation der Google Places-Daten ermöglicht.

## Funktionsweise

Der Cronjob nutzt die Methode `GooglePlaces::syncAll()` um alle in der Datenbank gespeicherten Places mit den aktuellen Daten aus der Google Places API zu synchronisieren.

## Klasse

```php
FriendsOfRedaxo\GooglePlaces\Cronjob
```

Die Klasse erweitert `rex_cronjob` und implementiert die erforderlichen Methoden für die Integration in das REDAXO Cronjob-System.

## Einrichtung

Der Cronjob wird automatisch bei der Installation des Addons angelegt, sofern das Cronjob-Addon verfügbar ist.

### Manuelle Einrichtung

Falls der Cronjob nicht automatisch angelegt wurde, kann er manuell im Backend unter **System → Cronjobs** eingerichtet werden:

1. Neuen Cronjob anlegen
2. Typ: **Google Places Sync** auswählen
3. Intervall nach Bedarf einstellen (z.B. täglich, wöchentlich)
4. Cronjob aktivieren

## Einstellungen

Die Synchronisation berücksichtigt die folgenden Einstellungen aus der Addon-Konfiguration:

### API-Schlüssel

Der Google Maps API-Schlüssel muss in den Addon-Einstellungen hinterlegt sein. Ohne API-Schlüssel schlägt die Synchronisation fehl.

### Review-Synchronisation

In den Addon-Einstellungen kann festgelegt werden, ob Reviews synchronisiert werden sollen:
- **Ja**: Reviews werden synchronisiert und in der Datenbank gespeichert
- **Nein**: Nur Place-Details werden synchronisiert, Reviews werden nicht abgerufen

### Auto-Publish für neue Reviews

Die Einstellung **Auto-Publish** legt fest, ob neue Reviews automatisch sichtbar geschaltet werden:
- **Sichtbar**: Neue Reviews sind sofort sichtbar (`status = 1`)
- **Ausgeblendet**: Neue Reviews müssen manuell freigeschaltet werden (`status = 0`)

## Fehlerbehandlung

Der Cronjob prüft vor der Ausführung, ob ein API-Schlüssel konfiguriert ist. Falls nicht, wird eine Fehlermeldung zurückgegeben und die Ausführung abgebrochen.

Fehler während der Synchronisation werden im REDAXO System-Log protokolliert. Der Cronjob gibt dann ebenfalls eine Fehlermeldung zurück.

## Empfohlene Intervalle

Die Google Places API hat Nutzungslimits. Empfohlene Cronjob-Intervalle:

- **Täglich**: Für wenige Places (1-5)
- **Wöchentlich**: Für viele Places (5-20)
- **Monatlich**: Bei begrenztem API-Kontingent

> **Hinweis:** Reviews bei Google werden nicht stündlich aktualisiert. Ein tägliches oder wöchentliches Intervall ist in der Regel ausreichend.

## Manuelle Synchronisation

Alternativ zum Cronjob können Places auch manuell synchronisiert werden:

### Im Backend

Unter **Google Places → Synchronisieren** können alle Places mit einem Klick synchronisiert werden.

### Per Code

```php
use FriendsOfRedaxo\GooglePlaces\GooglePlaces;

// Alle Places synchronisieren
$success = GooglePlaces::syncAll();

// Oder einzelnen Place synchronisieren
use FriendsOfRedaxo\GooglePlaces\Place;
$place = Place::get($id);
$success = $place->sync();
```

## Logs prüfen

Nach der Ausführung des Cronjobs sollten die Logs überprüft werden:

1. **System → Systemlog** aufrufen
2. Nach Einträgen mit "Google Places" oder "googleplaces" filtern
3. Fehler und Warnungen beachten

Typische Log-Einträge:
- Erfolgsmeldungen: Anzahl der erfolgreich synchronisierten Places
- Warnungen: API-Fehler, fehlende Places
- Fehler: Fehlende API-Schlüssel, Netzwerkfehler
