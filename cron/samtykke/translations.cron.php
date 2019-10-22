<?php
/**
 * Loads the ukmid.en.yml translation file from UKMNorge\UKMdelta.git
 * This file contains text used in eula, privacy policy which needs to be
 * standardized throughout the systems
 */
ini_set('display_errors',true);

// Dependencies
use UKMNorge\File\SimpleRemoteCache;

require_once('UKM/Autoloader.php');

// Init cache
$cache = new SimpleRemoteCache(
    dirname( dirname( __DIR__ ) ) . '/translations/',
    86400 // 24H
);

// Load file, and do nothing more
$data = $cache->load( 
    'ukmid.en.yml',
    'https://raw.githubusercontent.com/UKMNorge/UKMdelta/master/src/UKMNorge/DeltaBundle/Resources/translations/ukmid.en.yml'
);

echo 'done';