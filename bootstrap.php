<?php
 
/**
 * Set up environment for my plugin's tests suite.
 */
 
 define( 'WP_DEVELOP_DIR', '/Users/rija/Documents/wordpress-dev/trunk/' );

/**
 * The path to the WordPress tests checkout.
 */
define( 'WP_TESTS_DIR', '/Users/rija/Documents/wordpress-dev/trunk/tests/phpunit/' );
 
/**
 * The path to the main file of the plugin to test.
 */
define( 'TEST_PLUGIN_FILE', '/Users/rija/Documents/retreat-registration-plugin/src/retreat_registration.php' );
 
/**
 * The WordPress tests functions.
 *
 * We are loading this so that we can add our tests filter
 * to load the plugin, using tests_add_filter().
 */
require_once WP_TESTS_DIR . 'includes/functions.php';
 
/**
 * Manually load the plugin main file.
 *
 * The plugin won't be activated within the test WP environment,
 * that's why we need to load it manually.
 *
 * You will also need to perform any installation necessary after
 * loading your plugin, since it won't be installed.
 */
function _manually_load_plugin() {
 
    require TEST_PLUGIN_FILE;
 
    // Make sure plugin is installed here ...
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
 
/**
 * Sets up the WordPress test environment.
 *
 * We've got our action set up, so we can load this now,
 * and voila, the tests begin.
 */
//require WP_TESTS_DIR . 'includes/bootstrap.php';

/**
 * Sets up the Formidable test environment.
 *
 * If testing against Formidable Pro, the Pro files need to be copied into the local checkout
 * of Formidable forms and the composer.json needs updated to reference them
 */

require '../formidable-forms/tests/bootstrap.php';

