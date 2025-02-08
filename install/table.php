<?php

rex_sql_table::get(rex::getTable('googleplaces_review'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('place_detail_id', 'int(10) unsigned'))
    ->ensureColumn(new rex_sql_column('google_place_id', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('author_name', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('rating', 'int(11)', true))
    ->ensureColumn(new rex_sql_column('author_url', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('language', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('text', 'text'))
    ->ensureColumn(new rex_sql_column('profile_photo_url', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('profile_photo_base64', 'text'))
    ->ensureColumn(new rex_sql_column('createdate', 'datetime'))
    ->ensureColumn(new rex_sql_column('status', 'tinyint(1)'))
    ->ensureColumn(new rex_sql_column('publishdate', 'datetime'))
    ->ensureColumn(new rex_sql_column('updatedate', 'datetime'))
    ->ensureColumn(new rex_sql_column('uuid', 'varchar(36)'))
    ->ensureColumn(new rex_sql_column('time', 'varchar(191)', false, ''))
    ->ensureIndex(new rex_sql_index('uuid', ['uuid'], rex_sql_index::UNIQUE))
    ->ensureIndex(new rex_sql_index('google_place_id_author_url', ['google_place_id', 'author_url'], rex_sql_index::UNIQUE))
    ->ensure();

rex_sql_table::get(rex::getTable('googleplaces_place_detail'))
->ensurePrimaryIdColumn()
->ensureColumn(new rex_sql_column('place_id', 'varchar(191)', false, ''))
->ensureColumn(new rex_sql_column('api_response_json', 'mediumtext'))
->ensureColumn(new rex_sql_column('createdate', 'datetime'))
->ensureColumn(new rex_sql_column('updatedate', 'datetime'))
->ensureColumn(new rex_sql_column('review_ids', 'varchar(191)'))
->ensureIndex(new rex_sql_index('unique_index', ['place_id'], rex_sql_index::UNIQUE))
->ensure();

// unix timestamp to datetime
rex_sql::factory()
    ->setQuery('UPDATE rex_googleplaces_review SET `publishdate` = FROM_UNIXTIME(`time`) WHERE `publishdate` IS NULL OR `publishdate` = "0000-00-00 00:00:00"');
