# Klasse `Place` für Google Places

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = FriendsOfRedaxo\GooglePlaces\Place::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getPlaceId()`

Gibt den Wert für das Feld `place_id` (Place ID) zurück:

Beispiel:

```php
$dataset = Place::get($id);
echo $dataset->getPlaceId();
```

### `setPlaceId(mixed $value)`

Setzt den Wert für das Feld `place_id` (Place ID).

```php
$dataset = Place::create();
$dataset->setPlaceId($value);
$dataset->save();
```

### `getApiResponseJson(bool $asPlaintext = false)`

Gibt den Wert für das Feld `api_response_json` (API Response JSON) zurück:

Beispiel:

```php
$dataset = Place::get($id);
$text = $dataset->getApiResponseJson(true);
```

### `setApiResponseJson(mixed $value)`

Setzt den Wert für das Feld `api_response_json` (API Response JSON).

```php
$dataset = Place::create();
$dataset->setApiResponseJson($value);
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` (Zuletzt aktualisiert) zurück:

Beispiel:

```php
$dataset = Place::get($id);
$datetime = $dataset->getUpdatedate();
```

### `setUpdatedate(string $datetime)`

Setzt den Wert für das Feld `updatedate` (Zuletzt aktualisiert).

```php
$dataset = Place::create();
$dataset->setUpdatedate($datetime);
$dataset->save();
```
