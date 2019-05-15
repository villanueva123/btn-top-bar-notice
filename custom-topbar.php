<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://businesstechninjas.com
 * @since             1.0.0
 * @package           topbar_alert
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Top Bar Alert
 * Plugin URI:        https://businesstechninjas.com
 * Description:       This Plugin  is use to display custom alert Box on the pages
 * Version:           1.0.0
 * Author:            Business Tech Ninjas
 * Author URI:        https://businesstechninjas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       btn-fulfillments
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CUSTOM_TOPBAR_VERSION', '1.0.0' );

require plugin_dir_path( __FILE__ ) . 'admin/custom-topbar.php';

function myprefix_enqueue_scripts() {
	wp_enqueue_script( 'custom_js', plugins_url( 'js/custom-script.js', __FILE__ ), array( 'wp-util', 'underscore', 'jquery','wp-color-picker','jquery-ui-datepicker'), '', true  );
}
add_action( 'wp_enqueue_scripts', 'myprefix_enqueue_scripts' );


/**
 * Gets the instance of the `elite_promos` class.
 * This function is useful for quickly grabbing data from outside the plugin or class.
 *
 * Begins execution of the plugin on first call.
 *
 * @since  1.0.0
 * @access public
 * @return object
 */
function topbar_alert() {
	return topbar_alert::get_instance();
}

#Let's do this thang!
topbar_alert();
