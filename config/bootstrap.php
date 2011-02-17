<?php
define('MINERVA_APP_PATH', dirname(__DIR__));
define('MINERVA_LIBRARY_PATH', dirname(__DIR__) . '/libraries');

/**
 * This file contains the loading instructions for all class libraries used in the application,
 * including the Lithium core, and the application itself. These instructions include library names,
 * paths to files, and any applicable class-loading rules. Also includes any statically-loaded
 * classes to improve bootstrap performance.
 */
require __DIR__ . '/bootstrap/libraries.php';

/**
 * This file contains configurations for connecting to external caching resources, as well as
 * default caching rules for various systems within your application
 */
require __DIR__ . '/bootstrap/cache.php';

/**
 * Include this file if your application uses one or more database connections.
 */
require __DIR__ . '/bootstrap/connections.php';

/**
 * This file defines bindings between classes which are triggered during the request cycle, and
 * allow the framework to automatically configure its environmental settings. You can add your own
 * behavior and modify the dispatch cycle to suit your needs.
 */
require __DIR__ . '/bootstrap/action.php';

/**
 * This file contains configuration for session (and/or cookie) storage, and user or web service
 * authentication.
 */
require __DIR__ . '/bootstrap/session.php';

/**
 * This file contains your application's globalization rules, including inflections,
 * transliterations, localized validation, and how localized text should be loaded. Uncomment this
 * line if you plan to globalize your site.
 */
// require __DIR__ . '/bootstrap/g11n.php';

/**
 * This file contains configurations for handling different content types within the framework,
 * including converting data to and from different formats, and handling static media assets.
 */
require __DIR__ . '/bootstrap/media.php';

/**
 * This file configures console filters and settings, specifically output behavior and coloring.
 */
// require __DIR__ . '/bootstrap/console.php';

// Set the date so we don't get some warnings (should be in php.ini)
$tz = ini_get('date.timezone');
if(!$tz) {
	$tz = 'UTC';
}
date_default_timezone_set($tz); 

require __DIR__ . '/bootstrap/auth.php';

// This file sets some filters required for the CMS.
require __DIR__ . '/bootstrap/minerva_bootstrap.php';

// This sets up minerva's access system. Don't use it if you don't want.
require __DIR__ . '/bootstrap/access.php';
?>