# Copilot Instructions for Google Places Add-on

## Project Overview

This is a REDAXO 5 add-on that enables integration with the Google Places API. It allows users to retrieve and store information about Google Places entries including reviews, ratings, photos, opening hours, and other location data.

## Tech Stack

- **CMS**: REDAXO 5.17+
- **Language**: PHP 8.1+
- **Database**: Uses REDAXO's YForm ORM for data management
- **Required Add-ons**: YForm 4.0.0+, YForm Field 2.9.0+
- **Frontend**: Bootstrap 5 (for module examples)
- **API**: Google Places API

## Project Structure

```
/lib/                    # Core PHP classes
  ├── Place.php          # Place model (YForm dataset)
  ├── Review.php         # Review model (YForm dataset)
  ├── GooglePlaces.php   # API interaction class
  ├── Cronjob.php        # Automated synchronization
  └── Api/               # API-related classes
/pages/                  # Backend pages
/fragments/              # Output templates (Bootstrap 5)
/lang/                   # Language files (de_de, en_gb, es_es, fr_fr, it_it, nl_nl)
/install/                # Installation scripts and database setup
/assets/                 # CSS, images, and frontend assets
/docs/                   # Documentation files
```

## Key Concepts

### Database Tables
- `rex_googleplaces_place_detail` - Stores Google Places entries with full API responses as JSON
- `rex_googleplaces_review` - Stores individual reviews for each place

### Models
Both `Place` and `Review` extend `rex_yform_manager_dataset` (YForm ORM):
- Use method chaining for setters (e.g., `->setPlaceId($id)->setRating($rating)`)
- Use `::query()` for querying collections
- Use `::get($id)` for retrieving single records

### Namespace
All classes use the namespace: `FriendsOfRedaxo\GooglePlaces`

## Coding Conventions

### PHP Standards
- Follow PSR-12 coding style
- Use strict types where applicable
- Type hints for parameters and return types
- Use PHP 8.1+ features (enums, named arguments, etc.)

### REDAXO Conventions
- Use REDAXO's `rex_` prefixed classes for framework functions
- Internationalization: Use `rex_i18n::msg('key')` for translations
- Logging: Use `rex_logger::factory()->log(rex_log::LEVEL_ERROR, 'Error message');` for error logging, or `rex_logger::logException($e);` to log exceptions
- File operations: Use `rex_file::` and `rex_dir::` helpers
- Paths: Use `rex_path::` and `rex_url::` for consistent path handling

### YForm ORM Patterns
- Extend `rex_yform_manager_dataset` for models
- Use query builder: `Review::query()->where()->find()`
- Use `findOne()` for single results, `find()` for collections
- Define model relationships with `getRelatedDataset('place_detail_id')` (e.g., `$review->getRelatedDataset('place_detail_id');`)

### Code Documentation
- Use `/** @api */` annotation for public API methods
- Add DocBlocks for complex methods
- Keep comments in German for consistency with existing codebase comments

## Important Implementation Details

### API Integration
- API calls are handled through `GooglePlaces::getFromGoogle($placeId)`
- API responses are stored as JSON in the `api_response_json` field
- Profile photos are downloaded and stored in `/data/addons/googleplaces/profile_photos/`
- Google Places API limits reviews to the last 5 entries

### Configuration
- Settings stored via `rex_addon::get('googleplaces')->getConfig($key)`
- Key settings: `api_key`, `auto_publish_reviews`, `sync_reviews`

### Status Management
- Reviews have status: `Review::STATUS_VISIBLE` (1) or `Review::STATUS_HIDDEN` (0)
- Auto-publish can be enabled via configuration

### Synchronization
- Manual sync: `$place->sync()` method
- Automatic sync: Cronjob installed on add-on installation
- Sync creates/updates reviews based on UUID (generated from place_id + author_url)

## Testing and Validation

This project doesn't have a dedicated test suite. Changes should be validated by:
1. Installing the add-on in a REDAXO 5 instance
2. Testing the backend pages functionality
3. Verifying API calls work correctly
4. Checking cronjob execution
5. Testing frontend fragment output

## Deployment

- Releases are published to REDAXO.org via GitHub Actions
- See `.github/workflows/publish-to-redaxo-org.yml`
- Version is defined in `package.yml`

## Common Patterns

### Adding a new method to Place or Review
```php
/** @api */
public function getFieldName(): ?string
{
    return $this->getValue("field_name");
}

/** @api */
public function setFieldName(mixed $value): self
{
    $this->setValue("field_name", $value);
    return $this;
}
```

### Querying reviews
```php
// Returns a collection of Review objects
$reviews = Review::query()
    ->where('place_detail_id', $placeId)
    ->where('rating', 5, '>=')
    ->where('status', Review::STATUS_VISIBLE)
    ->orderBy('publishdate', 'DESC')
    ->limit(0, 10)
    ->find(); // Use findOne() for a single result
```

### Using fragments in modules
```php
$fragment = new rex_fragment();
$fragment->setVar('place', Place::get($id), false);
// Fragment path is relative to /fragments/ directory
echo $fragment->parse('googleplaces/reviews.bs5.php');
```

## Security Considerations

- API keys must be properly secured and restricted
- Validate API responses before storing
- Sanitize user input in backend forms
- Use parameterized queries (handled by YForm)
- Download external images with error handling

## Multi-language Support

The add-on supports multiple languages. When adding new features:
- Add translation keys to all language files in `/lang/`
- Use `rex_i18n::msg('googleplaces_your_key')` in code
- Keep translations consistent across languages
