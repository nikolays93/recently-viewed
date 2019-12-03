<?php

/**
 * Page content area output file
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\RecentlyViewed;

use NikolayS93\WPAdminForm\Form as Form;

// @var array $data id or name - required
$data = array(
	array(
		'id'          => 'cookie_expire',
		'type'        => 'text',
		'label'       => 'Cookie live time',
		'placeholder' => __( 'Default: 360', Plugin::DOMAIN ),
		'desc'        => '',
	),
	array(
		'id'          => 'number_of_posts',
		'type'        => 'text',
		'label'       => 'Max post list count',
		'placeholder' => __( 'Default: 12', Plugin::DOMAIN ),
		'desc'        => '',
	),
	array(
		'id'          => 'example_1',
		'type'        => 'select',
		'label'       => 'Post types (In future)',
		'options'     => array(
			'product' => 'Products only',
		),
	),
	array(
		'id'          => 'show_after_woocommerce_single_product',
		'type'        => 'checkbox',
		'label'       => 'Show it',
		'desc'        => 'Enable show event after woocommerce single product.',
	),
);

$form = new Form( $data, $is_table = true );
$form->display();

$settings = Plugin()->get_setting( '', array() );
array_walk(
	$settings,
	function ( $value, $key ) {
		echo esc_html( "$key: $value\r\n" );
	}
);

submit_button( 'Сохранить', 'primary', 'save_changes' );
echo '<div class="clear"></div>';
