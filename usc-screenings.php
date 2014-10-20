<?php
/**
 * USC Screenings Plugin, built using The WordPress Plugin Boilerplate.
 *
 *
 * @package   USC_Screenings
 * @author    Paul Craig <pcraig3@uwo.ca>
 * @license   GPL-2.0+
 * @copyright 2014
 *
 * @wordpress-plugin
 * Plugin Name:       USC Screenings
 * Plugin URI:        http://testwestern.com
 * Description:       Creates the 'Screening' Custom Post Type, shortcode to list them, and single-template
 * Version:           1.0.1
 * Author:            Paul Craig
 * Author URI:        https://profiles.wordpress.org/pcraig3/
 * Text Domain:       usc-screenings
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/pcraig3/usc-screenings
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * - replace `class-usc-screenings.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-usc-screenings.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'USC_Screenings', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'USC_Screenings', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'USC_Screenings', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-usc-screenings-admin.php' );
	add_action( 'plugins_loaded', array( 'USC_Screenings_Admin', 'get_instance' ) );

}
