# Klasse `Review` für Bewertungen eines Google Place

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$reviews = FriendsOfRedaxo\GooglePlaces\Review::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getPlace()`

Gibt das zugehörige `Place`-Objekt zurück:

Beispiel:

```php
$dataset = Review::get($id);
$place = $dataset->getPlace();
```

### `findFilter(string $place_id = null, int $limit = 5, int $offset = 0, int $minRating = 5, string $orderByField = 'publishdate', string $orderBy = 'DESC', int $status = self::STATUS_VISIBLE)`

Findet gefilterte Einträge basierend auf den angegebenen Parametern:

Beispiel:

```php
$reviews = Review::findFilter($place_id, $limit, $offset, $minRating, $orderByField, $orderBy, $status);
```

### `getStatusOptions()`

Gibt die möglichen Statusoptionen als Array zurück:

Beispiel:

```php
$statusOptions = Review::getStatusOptions();
```

### `getGooglePlaceId()`

Gibt den Wert für das Feld `google_place_id` (Google Place ID) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getGooglePlaceId();
```

### `setGooglePlaceId(mixed $value)`

Setzt den Wert für das Feld `google_place_id` (Google Place ID).

```php
$dataset = Review::create();
$dataset->setGooglePlaceId($value);
$dataset->save();
```

### `setPlaceId(int $value)`

Setzt den Wert für das Feld `place_detail_id` (Place ID).

```php
$dataset = Review::create();
$dataset->setPlaceId($value);
$dataset->save();
```

### `getPlaceId()`

Gibt den Wert für das Feld `place_detail_id` (Place ID) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getPlaceId();
```

### `getAuthorName()`

Gibt den Wert für das Feld `author_name` (Autor*in) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getAuthorName();
```

### `setAuthorName(mixed $value)`

Setzt den Wert für das Feld `author_name` (Autor*in).

```php
$dataset = Review::create();
$dataset->setAuthorName($value);
$dataset->save();
```

### `getRating()`

Gibt den Wert für das Feld `rating` (Bewertung) zurück:

Beispiel:

```php
$dataset = Review::get($id);
$int = $dataset->getRating();
```

### `setRating(int $value)`

Setzt den Wert für das Feld `rating` (Bewertung).

```php
$dataset = Review::create();
$dataset->setRating($value);
$dataset->save();
```

### `getAuthorUrl()`

Gibt den Wert für das Feld `author_url` (Autor*in URL) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getAuthorUrl();
```

### `setAuthorUrl(mixed $value)`

Setzt den Wert für das Feld `author_url` (Autor*in URL).

```php
$dataset = Review::create();
$dataset->setAuthorUrl($value);
$dataset->save();
```

### `getLanguage()`

Gibt den Wert für das Feld `language` (Sprache) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getLanguage();
```

### `setLanguage(mixed $value)`

Setzt den Wert für das Feld `language` (Sprache).

```php
$dataset = Review::create();
$dataset->setLanguage($value);
$dataset->save();
```

### `getText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `text` (Text) zurück:

Beispiel:

```php
$dataset = Review::get($id);
$text = $dataset->getText(true);
```

### `setText(mixed $value)`

Setzt den Wert für das Feld `text` (Text).

```php
$dataset = Review::create();
$dataset->setText($value);
$dataset->save();
```

### `getProfilePhotoUrl()`

Gibt den Wert für das Feld `profile_photo_url` (Profilbild URL) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getProfilePhotoUrl();
```

### `setProfilePhotoUrl(mixed $value)`

Setzt den Wert für das Feld `profile_photo_url` (Profilbild URL).

```php
$dataset = Review::create();
$dataset->setProfilePhotoUrl($value);
$dataset->save();
```

### `getProfilePhotoBase64(bool $asPlaintext = false)`

Gibt den Wert für das Feld `profile_photo_base64` (Profilbild Base64) zurück:

Beispiel:

```php
$dataset = Review::get($id);
$text = $dataset->getProfilePhotoBase64(true);
```

### `setProfilePhotoBase64(mixed $value)`

Setzt den Wert für das Feld `profile_photo_base64` (Profilbild Base64).

```php
$dataset = Review::create();
$dataset->setProfilePhotoBase64($value);
$dataset->save();
```

### `getPublishdate()`

Gibt den Wert für das Feld `publishdate` (Veröffentlicht am...) zurück:

Beispiel:

```php
$dataset = Review::get($id);
$datestamp = $dataset->getPublishdate();
```

### `setPublishdate(string $value)`

Setzt den Wert für das Feld `publishdate` (Veröffentlicht am...).

```php
$dataset = Review::create();
$dataset->setPublishdate($value);
$dataset->save();
```

### `getCreatedate()`

Gibt den Wert für das Feld `createdate` (Erstellt am...) zurück:

Beispiel:

```php
$dataset = Review::get($id);
$datestamp = $dataset->getCreatedate();
```

### `setCreatedate(string $value)`

Setzt den Wert für das Feld `createdate` (Erstellt am...).

```php
$dataset = Review::create();
$dataset->setCreatedate($value);
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` (Zuletzt aktualisiert am...) zurück:

Beispiel:

```php
$dataset = Review::get($id);
$datestamp = $dataset->getUpdatedate();
```

### `setUpdatedate(string $value)`

Setzt den Wert für das Feld `updatedate` (Zuletzt aktualisiert am...).

```php
$dataset = Review::create();
$dataset->setUpdatedate($value);
$dataset->save();
```

### `getUuid()`

Gibt den Wert für das Feld `uuid` (UUID) zurück:

Beispiel:

```php
$dataset = Review::get($id);
echo $dataset->getUuid();
```

### `setUuid(mixed $value)`

Setzt den Wert für das Feld `uuid` (UUID).

```php
$dataset = Review::create();
$dataset->setUuid($value);
$dataset->save();
```
