<?php
/**
 * Plugin Name: Recently viewed
 * Plugin URI: https://github.com/nikolays93
 * Description: Show last viewed posts, pages, products.. by cookie.
 * Version: 0.1.1
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: recently_viewed
 * Domain Path: /languages/
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\RecentlyViewed;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You shall not pass' );
}

if ( ! defined( __NAMESPACE__ . '\PLUGIN_DIR' ) ) {
	define( __NAMESPACE__ . '\PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
}

if ( ! function_exists( 'include_plugin_file' ) ) {
	/**
	 * Safe dynamic expression include.
	 *
	 * @param string $path relative path.
	 */
	function include_plugin_file( $path ) {
		if ( 0 !== strpos( $path, PLUGIN_DIR ) ) {
			$path = PLUGIN_DIR . $path;
		}
		if ( is_file( $path ) && is_readable( $path ) ) {
			return include $path; // phpcs:ignore
		}

		return false;
	}
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! include_once PLUGIN_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php' ) {
	array_map(
		__NAMESPACE__ . '\include_plugin_file',
		array(
			'include/class/Creational/Singleton.php',
			'include/class/Plugin.php',
			'include/class/Register.php',
		)
	);
}

/**
 * Returns the single instance of this plugin, creating one if needed.
 *
 * @return Plugin
 */
function plugin() {
	return Plugin::get_instance();
}

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\Plugin', 10 );
add_action(
	'plugins_loaded',
	function () {
		$register = new Register();
		$register->register_plugin_page();
	},
	20
);

add_action( 'get_header', array( __NAMESPACE__ . '\Register', 'init' ) );
add_action( 'woocommerce_after_single_product_summary', array( __NAMESPACE__ . '\Register', 'show_recently_products' ), 50 );

add_filter( 'woocommerce_product_loop_start', function( $html ) {
	global $is_recently_viewed_products;

	if( $is_recently_viewed_products ) {
		$html = '<h2>' . __( 'Recently viewed products', plugin::DOMAIN ) . '</h2>' . "\r\n" .  $html;
	}

	return $html;
}, 10, 1 );

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'activate' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'uninstall' ) );
