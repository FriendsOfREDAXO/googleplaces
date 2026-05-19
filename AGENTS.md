# Google Places - Redaxo Addon

Short rules. Core knowledge. No ballast.

## Core Rules

- Namespace: `FriendsOfRedaxo\GooglePlaces`
- PHP >= 8.1, REDAXO >= 5.17, YForm >= 4
- 4 spaces, camelCase for variables, PascalCase for classes
- Comments in German
- Public API methods with `/** @api */`
- Setters return `self` for method chaining
- Backend labels via `rex_i18n::msg()` with keys from `lang/`
- Always escape user output with `rex_escape()`
- Errors via `rex_logger`
- Protect state changes with `rex_csrf_token`

## Architecture

- `Place` and `Review` are YForm datasets
- Full Google response lives in `api_response_json`
- Profile photos live under `/data/addons/googleplaces/profile_photos/`
- Review UUID: `md5(place_id + author_url)`
- Sync flow: cronjob or `sync.php` -> `GooglePlaces::syncAll()` -> `Place::sync()`
- `Place::sync()` loads API data, stores JSON, creates new reviews, downloads profile photos, respects config
- Legacy Places API returns details and reviews, new Places API searches place IDs

## Data And Patterns

- Tables: `rex_googleplaces_place_detail`, `rex_googleplaces_review`
- Review status: visible or hidden
- Profile photo file path in `profile_photo_file`, Base64 only as legacy fallback
- Build queries with YForm query builder: `::query()`, `find()`, `findOne()`, `findFilter()`
- Place relation through `place_detail_id`

## Config

- Config via `rex_addon::get('googleplaces')->getConfig($key)`
- Relevant keys: `api_key`, `sync_reviews`, `auto_publish_reviews`

## Backend And Fragment

- Key classes: `Place`, `Review`, `GooglePlaces`, `Cronjob`, `Api\FindPlaceId`, legacy wrapper `gplace`
- Fragment `googleplaces/reviews.bs5.php` expects `place`, optional `limit`
- YForm lists adjusted through `YFORM_DATA_LIST`
- Default manager header hidden through `YFORM_MANAGER_DATA_PAGE_HEADER`

## When Changing

- Keep translation keys in sync across all files under `lang/`
- If new project insights matter for rules or pitfalls: update this file
- Use real umlauts in changelog files, AGENTS.md, and README.md

## Testing

- No automated suite
- At minimum check: install in REDAXO, backend pages, API/sync flow, cronjob, fragment output
