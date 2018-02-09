<?php

use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Cms\Site;

/**
 * Api Model Definitions
 */
return [
    'Avatar'        => include __DIR__ . '/models/Avatar.php',
    'File'          => include __DIR__ . '/models/File.php',
    'Locale'        => include __DIR__ . '/models/Locale.php',
    'Page'          => include __DIR__ . '/models/Page.php',
    'PageBlueprint' => include __DIR__ . '/models/PageBlueprint.php',
    'Site'          => include __DIR__ . '/models/Site.php',
    'SiteBlueprint' => include __DIR__ . '/models/SiteBlueprint.php',
    'User'          => include __DIR__ . '/models/User.php',
    'UserBlueprint' => include __DIR__ . '/models/UserBlueprint.php',
];
