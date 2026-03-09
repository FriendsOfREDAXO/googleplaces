# Google Places - Redaxo Addon

A REDAXO 5 addon that integrates with the Google Places API to retrieve and store place details, reviews, ratings, photos, opening hours, and other location data. Built on YForm ORM with full multilingual support.

## Tech Stack

- **Language:** PHP >= 8.1
- **CMS:** REDAXO >= 5.17.0
- **ORM:** YForm >= 4.0.0
- **Frontend Framework:** Bootstrap 5 (fragments)
- **API:** Google Places API (Legacy + New)
- **Namespace:** `FriendsOfRedaxo\GooglePlaces`

## Project Structure

```text
googleplaces/
├── boot.php               # YForm model registration, cronjob type, extension points
├── install.php             # Installation routine (config migration, table setup, cronjob)
├── update.php              # Update handler (includes install.php)
├── uninstall.php           # Full cleanup (YForm tables, DB tables, cronjob, data, config)
├── package.yml             # Addon metadata, version, dependencies, default config
├── README.md
├── AGENTS.md
├── .github/
│   ├── copilot-instructions.md
│   └── workflows/          # GitHub Actions (publish to redaxo.org)
├── assets/
│   ├── css/                # Component styling
│   └── img/                # Images/icons
├── docs/                   # 9 markdown documentation files
├── fragments/
│   └── googleplaces/
│       └── reviews.bs5.php # Bootstrap 5 review display fragment
├── install/
│   ├── table.php           # Raw SQL table creation (fallback)
│   ├── tableset.php        # YForm table imports + migration
│   ├── googleplaces_place_detail.tableset.json
│   ├── googleplaces_review.tableset.json
│   └── cronjob_sync.php    # Cronjob registration
├── lang/                   # 6 language files (de_de, en_gb, es_es, fr_fr, it_it, nl_nl)
├── lib/
│   ├── Place.php           # Place model (rex_yform_manager_dataset)
│   ├── Review.php          # Review model (rex_yform_manager_dataset)
│   ├── GooglePlaces.php    # Static API client class
│   ├── Cronjob.php         # rex_cronjob for automated sync
│   ├── gplace.php          # Deprecated backward compatibility wrapper
│   └── Api/
│       └── FindPlaceId.php # rex_api_function for place search
└── pages/
    ├── index.php           # Page router
    ├── config.php          # Settings form (API key, sync, auto-publish)
    ├── place_detail.php    # Place management (YForm manager)
    ├── review.php          # Review management (YForm manager)
    ├── query.php           # Place search UI (POST + CSRF)
    ├── sync.php            # Manual sync trigger
    └── docs.php            # Documentation viewer (markdown from docs/)
```

## Coding Conventions

- **Namespace:** `FriendsOfRedaxo\GooglePlaces` for all classes
- **Naming:** camelCase for variables, PascalCase for classes
- **Indentation:** 4 spaces
- **Comments:** German comments (consistent with existing codebase)
- **API annotation:** Use `/** @api */` for public API methods
- **Method chaining:** All setter methods return `self`
- **Backend labels:** Use `rex_i18n::msg()` with keys from `lang/` files
- **Output escaping:** Use `rex_escape()` for all user-facing output
- **Error logging:** Use `rex_logger::factory()->log('error', $msg, [], __FILE__, __LINE__)`
- **CSRF protection:** State-changing pages use `rex_csrf_token`

## AGENTS.md Maintenance

- When new project insights are gained during work and they are relevant to agent guidance, workflows, conventions, architecture, or known pitfalls, update this AGENTS.md accordingly.

## Key Classes

| Class | Description |
| ----- | ----------- |
| `Place` | Place model: Google Place ID, full API response JSON, reviews, ratings, sync logic |
| `Review` | Review model: author, rating, text, profile photo, status (visible/hidden), UUID |
| `GooglePlaces` | Static API client: cURL calls to Google Places API, sync orchestration |
| `Cronjob` | Automated daily sync via `rex_cronjob` |
| `FindPlaceId` | `rex_api_function` for searching places via Google Places API (New) |
| `gplace` | Deprecated wrapper class for backward compatibility with v2.x |

## Database Tables

| Table | Description |
| ----- | ----------- |
| `rex_googleplaces_place_detail` | Place entries with Google Place ID, full API response as JSON, timestamps |
| `rex_googleplaces_review` | Individual reviews: author, rating, text, profile photo (file/base64/URL), status, UUID |

### Key Fields

**Place:**

- `place_id` (varchar, unique) — Google Places ID (e.g. `ChIJ...`)
- `api_response_json` (mediumtext) — Full Google API response stored as JSON
- `createdate`, `updatedate` (datetime)

**Review:**

- `place_detail_id` (int, FK) — Foreign key to place_detail.id
- `google_place_id` (varchar) — Duplicate of place.place_id for convenience
- `uuid` (varchar, unique) — Generated as `md5(place_id + author_url)`
- `status` (tinyint) — 0=hidden, 1=visible
- `profile_photo_file` (varchar) — Filename stored in `/data/addons/googleplaces/profile_photos/`
- `publishdate` (datetime) — From Google API timestamp

## Architecture

### API Integration

Two Google APIs are used:

1. **Places API (Legacy)** — `maps.googleapis.com/maps/api/place/details/json`
   - Used by `GooglePlaces::googleApiResult()` for fetching place details and reviews
   - Returns up to 5 most recent reviews

2. **Places API (New)** — `places.googleapis.com/v1/places:searchText`
   - Used by `FindPlaceId::queryPlaces()` for searching places by name/address
   - Returns place IDs, display names, and addresses

### Sync Flow

1. `Cronjob::execute()` or manual `sync.php` triggers `GooglePlaces::syncAll()`
2. `syncAll()` iterates all Place records and calls `$place->sync()` on each
3. `Place::sync()`:
   - Calls `GooglePlaces::googleApiResult($placeId)` to fetch from Google
   - Stores the full API JSON response on the Place record
   - Iterates reviews from the response
   - Generates UUID per review: `md5(place_id + author_url)`
   - Creates new Review records (skips existing by UUID)
   - Downloads profile photos to `/data/addons/googleplaces/profile_photos/`
   - Respects `sync_reviews` and `auto_publish_reviews` config

### Extension Points

| Extension Point | Location | Purpose |
| --------------- | -------- | ------- |
| `YFORM_DATA_LIST` | boot.php | Formats Place and Review columns in YForm backend lists |
| `YFORM_MANAGER_DATA_PAGE_HEADER` | place_detail.php, review.php | Hides default header |

### Data Storage

- **API responses:** Full JSON stored in `api_response_json` field on Place
- **Profile photos:** Downloaded as files to `/data/addons/googleplaces/profile_photos/{uuid}.jpg`
- **Legacy support:** Base64 encoded photos in `profile_photo_base64` field (deprecated)

## Configuration

Stored via `rex_addon::get('googleplaces')->getConfig($key)`:

| Key | Type | Default | Purpose |
| --- | ---- | ------- | ------- |
| `api_key` | string | `""` | Google Places API key (required) |
| `sync_reviews` | bool | `true` | Whether to sync reviews from Google |
| `auto_publish_reviews` | bool | `false` | Auto-set status to visible for new reviews |

## Fragments

### `googleplaces/reviews.bs5.php`

Bootstrap 5 review display. Parameters:

| Variable | Type | Default | Purpose |
| -------- | ---- | ------- | ------- |
| `place` | Place | required | Place object to display reviews for |
| `limit` | int | 3 | Number of reviews to show |

Usage:

```php
$fragment = new rex_fragment();
$fragment->setVar('place', Place::get($id), false);
echo $fragment->parse('googleplaces/reviews.bs5.php');
```

## Common Patterns

### Querying Reviews

```php
$reviews = Review::query()
    ->where('place_detail_id', $placeId)
    ->where('rating', 5, '>=')
    ->where('status', Review::STATUS_VISIBLE)
    ->orderBy('publishdate', 'DESC')
    ->limit(0, 10)
    ->find();
```

### Using findFilter

```php
$reviews = Review::findFilter(
    place_id: $place->getId(),
    limit: 5,
    minRating: 4,
    status: Review::STATUS_VISIBLE
);
```

### Method Chaining (Setters)

```php
$review->setPlaceId($placeId)
    ->setAuthorName($name)
    ->setRating($rating)
    ->setText($text)
    ->setStatus(Review::STATUS_VISIBLE)
    ->save();
```

## Multi-language Support

6 language files with 70+ translation keys each:

- `de_de.lang` — German
- `en_gb.lang` — English
- `es_es.lang` — Spanish
- `fr_fr.lang` — French
- `it_it.lang` — Italian
- `nl_nl.lang` — Dutch

When adding features, add translation keys to **all** language files using `rex_i18n::msg('googleplaces_your_key')`.

## Dependencies

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `yform` | >= 4.0.0 | ORM for Place and Review models |

## Versioning

This addon follows [Semantic Versioning](https://semver.org/):

- **Major** (1st digit): Breaking changes (e.g. removed classes, renamed methods, incompatible DB changes)
- **Minor** (2nd digit): New features, new modules, new database fields (backward compatible)
- **Patch** (3rd digit): Bug fixes, small improvements (backward compatible)

The version number is maintained in `package.yml`. Releases are published to REDAXO.org via GitHub Actions (`.github/workflows/publish-to-redaxo-org.yml`).

## Testing

No dedicated test suite. Validate changes by:

1. Installing the addon in a REDAXO 5 instance
2. Testing backend pages (config, places, reviews, search, sync)
3. Verifying API calls and sync
4. Checking cronjob execution
5. Testing fragment output in frontend modules
