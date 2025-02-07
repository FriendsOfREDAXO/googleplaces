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

### Namespace

Das Add-on wurde in den Namespace `FriendsOfRedaxo\GooglePlaces` verschoben.

### Klassen und Methoden

Die Klassen und Methoden wurden überarbeitet und sind nun YOrm-basiert. Die Methoden sind in der Dokumentation beschrieben.

### Fragmente

Die Fragmente wurden überarbeitet, ein neues Bootstrap 5-Fragment gibt es ebenfalls. Die  Modul-Beispiele wurden entsprechend angepasst. Die Fragmente sind im Ordner `fragments/googleplaces` zu finden. Ggf. müssen die Pfade zu den Fragmenten in Ausgabe-Modulen angepasst werden.
