# Klasse `Place` für Google Places

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_googleplaces_place_detail` zu.

> Es werden nachfolgend nur die durch dieses Addon ergänzten Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

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

## YRewrite-Domains (optional)

Ist das Add-on [YRewrite](https://github.com/yakamara/yrewrite) verfügbar, kann ein Google-Places-Eintrag im Backend **optional einer oder mehreren YRewrite-Domains** zugeordnet werden (Checkbox-Feld „Domains (YRewrite)"). Das ist besonders in Multi-Domain-Installationen nützlich, um pro Domain den passenden Eintrag (z. B. für Bewertungs-Widgets oder JSON-LD-Schema) zu ermitteln. Ohne YRewrite wird das Feld nicht angelegt; die Zuordnung ist nie Pflicht.

### `getDomainIds()`

Gibt die zugeordneten YRewrite-Domain-IDs als Array zurück:

```php
$dataset = Place::get($id);
$domainIds = $dataset->getDomainIds(); // z. B. [1, 3]
```

### `setDomainIds(array|string $value)`

Setzt die zugeordneten YRewrite-Domain-IDs (Array oder Komma-Liste):

```php
$dataset = Place::get($id);
$dataset->setDomainIds([1, 3]);
$dataset->save();
```

### `hasDomain(int $domainId)`

Prüft, ob der Place einer bestimmten YRewrite-Domain zugeordnet ist:

```php
$dataset = Place::get($id);
if ($dataset->hasDomain(2)) {
    // ...
}
```

### `findByDomainId(int $domainId)`

Gibt alle Places einer YRewrite-Domain als Collection zurück:

```php
$places = Place::findByDomainId(2);
```

### `getForDomain(?int $domainId = null)`

Gibt den (ersten) Place einer YRewrite-Domain zurück. Ohne Parameter wird die **aktuelle Domain** verwendet. Gibt `null` zurück, wenn YRewrite nicht verfügbar ist oder der Domain kein Place zugeordnet wurde:

```php
/* Im Template/Modul: der Place der aktuell aufgerufenen Domain */
$place = Place::getForDomain();

/* Oder explizit per Domain-ID */
$place = Place::getForDomain(2);

if ($place !== null) {
    echo $place->getName();
}
```
