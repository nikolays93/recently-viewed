<?php
/**
 * Register plugin actions
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\RecentlyViewed;

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
		if( is_singular( plugin()->get_setting( 'post_type' ) ) ) {
			static::update_cookie();
		}
	}

	/**
	 * Call this method before activate plugin
	 */
	public static function activate() {
		plugin()->set_setting( array(
			'post_type' => array( 'product' ),
			'show_after_woocommerce_single_product' => 'on',
		) );
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
	 * Call this method after show product's single page
	 */
	public static function show_recently_products() {
		global $is_recently_viewed_products;

		if( false === plugin()->get_setting('show_after_woocommerce_single_product') ) return null;

		/** @var array||null string  Get cookie ids */
		$cookie_ids = Plugin::unserialize_cookie( plugin()->get_cookie() );
		/** @var int Current post ID */
		$post_id = get_the_ID();
		/** Filter (unset) curret post ID */
		$cookie_ids = array_filter( $cookie_ids, function( $val ) use ( $post_id ) {
			return $val !== $post_id;
		} );

		if( empty( $cookie_ids ) ) return null;

		$query = new \WP_Query( array(
			'post__in' => $cookie_ids,
			'post_type' => 'product',
			'orderby' => 'post__in',
			'order'   => 'ASC',
		) );

		if ( $query->have_posts() && function_exists( 'wc_get_template_part' ) ) :
			$is_recently_viewed_products = true;

			?>
			<section class="recently-viewed-products">

				<?php
				woocommerce_product_loop_start();

			    while ( $query->have_posts() ) {
			        $query->the_post();

			        /**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
			    }

			    woocommerce_product_loop_end();
			    ?>
			</section>
			<?php
			$is_recently_viewed_products = false;
		endif;
		wp_reset_postdata();
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
			__( 'Recently viewed settings', Plugin::DOMAIN ),
			array(
				'parent'      => 'options-general.php',
				'menu'        => __( 'Recently viewed', Plugin::DOMAIN ),
				'permissions' => $plugin->get_permissions(),
				'columns'     => 1,
			)
		);

		$page->set_content(
			function () use ( $plugin ) {
				include_plugin_file( $plugin->get_template( 'admin/template/menu-page' ) );
			}
		);

		// $page->set_assets(
		// 	function () use ( $plugin ) {
		// 	}
		// );

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

		$cookie_value = serialize( array_unique( $cookie_ids ) );

		/** @var int Cookie deadline time */
		$expire = time() + ( plugin()->get_setting( 'cookie_expire', 72 ) * 3600 );

		$home_url_array = parse_url( home_url() );
		/** @var string domain fullname */
		$host = isset( $home_url_array['host'] ) ? $home_url_array['host'] : '';
		/** @var string after domain side url */
		$path = isset( $home_url_array['path'] ) ? trailingslashit( $home_url_array['path'] ) : '/';

		/** @var string Domain from home option */
		$domain = str_replace('www.', '', $host);

		setcookie( plugin()->get_cookie_name(), $cookie_value, $expire, $path, ".$domain", 0 );
	}
}
