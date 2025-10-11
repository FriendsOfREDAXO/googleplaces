# Upgrade von Version 2.X auf 3.X

## Vor dem Upgrade

### Backup

Vor dem Upgrade sollte ein Backup der Datenbank und der Dateien erstellt werden.

### Cronjob

Wenn der Cronjob für die automatische Synchronisation bereits eingerichtet wurde, sollte dieser deaktiviert werden, bevor das Upgrade durchgeführt wird.

## Upgrade

### Add-on aktualisieren

Das Add-on kann über den Installer aktualisiert werden. Es wird empfohlen, das Vorgänger-Add-on **nicht zu deinstallieren**, um Datenverlust zu vermeiden.

### Datenbank aktualisieren

Während des Upgrades wird die Datenbank automatisch aktualisiert. Es wird empfohlen, die Datenbank nach dem Upgrade zu überprüfen, ggf. müssen bestehende Rezensionen einem Google Place zugeordnet werden.

### Konfiguration überprüfen

Die Konfiguration des Add-ons sollte überprüft werden, insbesondere die API-Schlüssel und Place-IDs.

## Änderungen

### Version 3.1: Profilbilder im Dateisystem

Ab Version 3.1 werden Profilbilder der Rezensenten nicht mehr als Base64-String in der Datenbank gespeichert, sondern als Dateien im Dateisystem unter `redaxo/data/addons/googleplaces/profile_photos/`. Dies reduziert die Datenbankgröße erheblich und verbessert die Performance.

**Wichtig:** Bestehende Base64-Profilbilder bleiben in der Datenbank erhalten und werden weiterhin angezeigt (Rückwärtskompatibilität). Bei der nächsten Synchronisation werden die Profilbilder automatisch ins Dateisystem übertragen.

Die neue Methode `getProfilePhotoSrc()` liefert automatisch die richtige Bildquelle:
- Bevorzugt wird die Datei aus dem Dateisystem verwendet
- Falls nicht vorhanden, wird auf das Base64-Bild zurückgegriffen
- Falls auch das nicht vorhanden ist, wird `null` zurückgegeben

**Empfohlene Maßnahmen nach dem Upgrade:**
1. Führen Sie eine Synchronisation aller Places durch, um die Profilbilder ins Dateisystem zu übertragen
2. Optional: Bereinigen Sie nach erfolgreicher Migration die Base64-Daten aus der Datenbank mit: 
   ```sql
   UPDATE rex_googleplaces_review SET profile_photo_base64 = NULL WHERE profile_photo_file IS NOT NULL AND profile_photo_file != '';
   ```

### Namespace

Das Add-on wurde in den Namespace `FriendsOfRedaxo\GooglePlaces` verschoben.

### Klassen und Methoden

Die Klassen und Methoden wurden überarbeitet und sind nun YOrm-basiert. Die Methoden sind in der Dokumentation beschrieben.

### Fragmente

Die Fragmente wurden überarbeitet, ein neues Bootstrap 5-Fragment gibt es ebenfalls. Die  Modul-Beispiele wurden entsprechend angepasst. Die Fragmente sind im Ordner `fragments/googleplaces` zu finden. Ggf. müssen die Pfade zu den Fragmenten in Ausgabe-Modulen angepasst werden.
