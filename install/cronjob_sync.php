<?php

/**
 * Job "Cronjob" konfigurieren.
 */

use FriendsOfRedaxo\GooglePlaces\Cronjob;

$job_intervall = [
    'minutes' => [0],
    'hours' => [0],
    'days' => 'all',
    'weekdays' => 'all',
    'months' => 'all',
];

$timestamp = rex_cronjob_manager_sql::calculateNextTime($job_intervall);

$sql = rex_sql::factory();
$sql->setTable(rex::getTable('cronjob'));
$sql->setValue('name', '[googleplaces] Google Places Daten synchronisieren');
$sql->setValue('description', 'Synchronisiert die Google Places Daten.');
$sql->setValue('type', Cronjob::class);
$sql->setValue('parameters', '[]');
$sql->setValue('interval', json_encode($job_intervall));
$sql->setValue('nexttime', rex_sql::datetime($timestamp));
$sql->setValue('environment', '|frontend|backend|script|');
$sql->setValue('execution_moment', 0);
$sql->setValue('execution_start', '0000-00-00 00:00:00');
$sql->setValue('status', 1);
$sql->addGlobalUpdateFields('googleplaces');
$sql->addGlobalCreateFields('googleplaces');
$sql->insert();
