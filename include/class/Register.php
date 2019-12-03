<?php
/**
 * Register plugin actions
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\PluginName;

use NikolayS93\WPAdminPage\Page;
use NikolayS93\WPAdminPage\Section;
use NikolayS93\WPAdminPage\Metabox;

/**
 * Class Register
 */
class Register {

	/**
	 * Init Wordpress before page's header event (action)
	 */
	public static function init() {
		// @TODO: add post type choice.
		if( is_singular( array('product') ) ) {
			static::update_cookie();
		}
	}

	/**
	 * Call this method before activate plugin
	 */
	public static function activate() {
	}

	/**
	 * Call this method before disable plugin
	 */
	public static function deactivate() {
	}

	/**
	 * Call this method before delete plugin
	 */
	public static function uninstall() {
	}

	/**
	 * Register new admin menu item
	 *
	 * @return Page $Page
	 */
	public function register_plugin_page() {
		$plugin = plugin();

		$page = new Page(
			$plugin->get_option_name(),
			__( 'New Plugin name Title', Plugin::DOMAIN ),
			array(
				'parent'      => '', // for ex. woocommerce.
				'menu'        => __( 'Example', Plugin::DOMAIN ),
				'permissions' => $plugin->get_permissions(),
				'columns'     => 2,
			)
		);

		$page->set_content(
			function () use ( $plugin ) {
				include_plugin_file( $plugin->get_template( 'admin/template/menu-page' ) );
			}
		);

		$page->add_section(
			new Section(
				'section',
				__( 'Section', Plugin::DOMAIN ),
				$plugin->get_template( 'admin/template/section' )
			)
		);

		$page->add_metabox(
			new Metabox(
				'metabox',
				__( 'MetaBox', Plugin::DOMAIN ),
				$plugin->get_template( 'admin/template/metabox' ),
				$position = 'side',
				$priority = 'high'
			)
		);

		$page->set_assets(
			function () use ( $plugin ) {
			}
		);

		return $page;
	}

	private static function update_cookie() {
		/** @var int Current post id */
		$post_id = get_the_ID();
		/** @var int Count posts in array max size */
		$number_of_posts = plugin()->get_setting( 'number_of_posts', 12 );
		/** @var array||null string  Get cookie ids */
		$cookie_ids = Plugin::unserialize_cookie( plugin()->get_cookie() );

		// If the cookie is empty.
		if( ! is_array( $cookie_ids ) || empty( $cookie_ids ) ) {
			$cookie_ids = array( $post_id );
		}
		// If the item ID is already included in the array then remove it.
		elseif( in_array( $post_id, $cookie_ids ) ) {
			$current_key = array_search( $post_id, $cookie_ids );
			array_splice( $cookie_ids, $current_key, 1 );
		}

		// Insert new item ID.
		array_unshift( $cookie_ids, $post_id );

		// Remove out of number_of_posts count.
		while ( count($cookie_ids) > $number_of_posts ) {
			array_pop( $cookie_ids );
		}

		$home_url_array = parse_url( home_url() );

		if( ! isset( $home_url_array['host'] ) || ! isset( $home_url_array['path'] ) ) {
			return null;
		}

		/** @var string Domain from home option */
		$domain = str_replace('www.', '', $home_url_array['host']);
		/** @var string Home page url */
		$url = trailingslashit( $home_url_array['path'] );
		/** @var int Cookie deadline time */
		$expire = time() + (plugin()->get_setting( 'cookie_expire', 360 ) * 86400);

		setcookie( plugin()->get_cookie_name(), serialize($cookie_ids), $expire, $url, ".$domain", 0 );
	}
}
