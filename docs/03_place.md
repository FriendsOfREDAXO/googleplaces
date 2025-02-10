# Klasse `Place` für Google Places

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = FriendsOfRedaxo\GooglePlaces\Place::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getReviews(int $limit = 100, int $offset = 0, int $minRating = 5, string $orderByField = 'publishdate', string $orderBy = 'DESC', int $status = Review::STATUS_VISIBLE)`

Gibt die zugehörigen `Review`-Objekte zurück:

Beispiel:

```php
$dataset = Place::get($id);
$reviews = $dataset->getReviews();
```

### `getApiResponseAsArray()`

Gibt den Wert für das Feld `api_response_json` als Array zurück:

Beispiel:

```php
$dataset = Place::get($id);
$apiResponseArray = $dataset->getApiResponseAsArray();
```

### `sync()`

Synchronisiert die Place-Daten mit den Google Places API-Daten:

Beispiel:

```php
$dataset = Place::get($id);
$success = $dataset->sync();
```

### `countReviews()`

Gibt die Anzahl der zugehörigen `Review`-Objekte zurück:

Beispiel:

```php
$dataset = Place::get($id);
$count = $dataset->countReviews();
```

```
### `getAvgRatingDb()`

Gibt den durchschnittlichen Bewertungswert aus der Datenbank zurück:

Beispiel:

```php
$dataset = Place::get($id);
$avgRatingDb = $dataset->getAvgRatingDb();
```

### `getAvgRatingApi()`

Gibt den durchschnittlichen Bewertungswert aus der Google Places API zurück:

Beispiel:

```php
$dataset = Place::get($id);
$avgRatingApi = $dataset->getAvgRatingApi();
```

### `getName()`

Gibt den Namen des Place aus der Google Places API zurück:

Beispiel:

```php
$dataset = Place::get($id);
$name = $dataset->getName();
```

### `getAddress()`

Gibt die Adresse des Place aus der Google Places API zurück:

Beispiel:

```php
$dataset = Place::get($id);
$address = $dataset->getAddress();
```

```
### `getPlacesOptions()`

Gibt die Places-Einträge als Array zurück:

Beispiel:

```php
$placesOptions = Place::getPlacesOptions();
```

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

### `getApiResponseJson()`

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
