<?php

/**
 * Plugin Name: Admin menu page
 * Description: Render a custom admin page.
 * Version: 1.0.1
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NikolayS93\WPAdminPage\Page' ) ) {
	include_once __DIR__ . '/src/Preset.php';
	include_once __DIR__ . '/src/Util.php';
	include_once __DIR__ . '/src/Page.php';
	include_once __DIR__ . '/src/Section.php';
	include_once __DIR__ . '/src/Metabox.php';
	include_once __DIR__ . '/src/Screen.php';
}
