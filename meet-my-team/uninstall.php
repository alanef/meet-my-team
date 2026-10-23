<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Meet My Team
 * @author    Aaron Lee <aaron.lee@buooy.com>
 * @license   GPL-2.0+
 * @link      http://buooy.com
 * @copyright 2014 Buooy
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove the opt-in library's stored choice. Since free_plugin_lib 1.3.0 the host plugin must do this.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
\Fullworks_Free_Plugin_Lib\Main::plugin_uninstall( 'MMT-Free' );
