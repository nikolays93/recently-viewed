<?php

namespace NikolayS93\WPAdminPage;

class Page {
	/** @var String Page slug name */
	protected $slug;

	/** @var String Page h2 title */
	private $title;

	/** @var String Wordpress screen slug name (for metaboxes) */
	protected $wp_screen_name;

	/** @var array Page parameters */
	protected $args;

	/** @var Bool Page has one or more sections */
	static public $has_sections;

	/** @var Bool Page has one or more metaboxes */
	static public $has_metaboxes;

	/**
	 * @return bool|String
	 */
	public function get_wp_screen_name() {
		$screen = false;
		if ( $this->wp_screen_name ) {
			$screen = $this->wp_screen_name;
		}

		return $screen;
	}

	/**
	 * Set page assets
	 *
	 * @param Callback
	 */
	public function set_assets( $callback ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] == $this->slug && is_callable( $callback ) ) {
			add_action( 'admin_enqueue_scripts', $callback );
		}
	}

	/**
	 * Add action "page body" callback
	 *
	 * @param Callback
	 */
	public function set_content( $callback ) {
		if ( $callback = Util::switch_to_callable( $callback ) ) {
			add_action( $this->slug . '_inside_page_content', $callback, 10 );
		}
	}

	/**
	 * Add wordpress post metabox for custom page
	 *
	 * @param Metabox $metabox
	 */
	public function add_metabox( Metabox $metabox ) {
		$metabox->init_on( $this );
		self::$has_metaboxes = true;
	}

	/**
	 * Add Tabs with tab's panes (set the panes after content)
	 *
	 * @param Section $section
	 */
	public function add_section( Section $section ) {
		add_action( $this->slug . '_inside_page_content', array( $section, 'tab_button' ), 20 );
		add_action( 'after_' . $this->slug . '_inside_page_content', array( $section, 'tab_pane' ), 20 );
		self::$has_sections = true;
	}

	/**
	 *
	 * @param String $slug Page::$slug setter
	 * @param String $title Setter for $title
	 * @param array $args Page global parameters @see add_menu_page or add_submenu_page
	 *                          parent      - parent menu slug for submenu page
	 *                          menu        - admin menu page title
	 *                          menu_pos    - position on main admin menu
	 *                          permissions - user role or access actions
	 *                          columns     - default/max columns of page
	 *                          icon_url    - custom icon or dashicon url
	 */
	function __construct( $slug = null, $title = null, $args = array() ) {
		// slug required
		if ( ! $slug ) {
			wp_die( 'You have false slug in admin page class in ' . __FILE__, 'Slug is false or empty' );
		}

		// reset
		self::$has_sections  = false;
		self::$has_metaboxes = false;

		$this->slug  = $slug;
		$this->title = $title;

		// merge with defaults
		$this->args = Preset::parse_page_args( $args );

		add_action( 'admin_init', array( $this, '__register_page_option' ) );
		add_action( 'admin_menu', array( $this, '__create_screen' ) );
	}

	/**
	 * Register page::slug option
	 */
	function __register_page_option() {
		/** @todo Add validate Callback */
		register_setting( $this->slug, $this->slug, $this->args['validate'] );
	}

	/**
	 * Add page wordpress handle
	 *
	 * @see wordpress codex: add_submenu_page()
	 */
	function __create_screen() {
		$screen               = new Screen( $this->slug, $this->title, $this->args );
		$this->wp_screen_name = $screen->__toString();

		add_action( 'load-' . $this->wp_screen_name, array( $this, '__page_actions' ), 9 );
		add_action( 'admin_footer-' . $this->wp_screen_name, array( $this, 'footer_scripts' ) );
	}

	/**
	 * Init actions for created page
	 */
	function __page_actions() {
		add_action( $this->slug . '_inside_side_container', array( $this, 'side_render' ), 10 );

		add_action( $this->slug . '_inside_normal_container', array( $this, 'normal_render' ), 10 );
		add_action( $this->slug . '_inside_advanced_container', array( $this, 'advanced_render' ), 10 );

		$post = new \stdClass();
		$post = new \WP_Post( $post );

		do_action( 'add_meta_boxes_' . $this->wp_screen_name, $post );
		do_action( 'add_meta_boxes', $this->wp_screen_name, $post );

		add_screen_option( 'layout_columns', array(
				'max'     => $this->args['columns'],
				'default' => $this->args['columns']
			)
		);

		// Enqueue WordPress' script for handling the metaboxes
		wp_enqueue_script( 'postbox' );
	}

	function side_render() {

		do_meta_boxes( $this->wp_screen_name, 'side', null );
	}

	function normal_render() {

		do_meta_boxes( $this->wp_screen_name, 'normal', null );
	}

	function advanced_render() {

		do_meta_boxes( $this->wp_screen_name, 'advanced', null );
	}

	function footer_scripts() {
		?>
        <script>
            jQuery(document).ready(function ($) {
                // Init metabox drug'n'drop
                postboxes.add_postbox_toggles(pagenow);
            });
        </script>
		<?php
		if ( ! empty( self::$has_sections ) ):
			?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('a.nav-tab').on('click', function (e) {
                        e.preventDefault();
                        var tab = $(this).data('tab'),
                            $active = $('.nav-tab-active'),
                            location = window.location.href.split('&tab')[0] + '&tab=' + tab;

                        // If already active
                        if ($(this).hasClass('nav-tab-active'))
                            return false;

                        // Url manipulation
                        if (tab) {
                            history.replaceState(null, null, location);
                            $('input[name="_wp_http_referer"]').val(location + '&settings-updated=true');
                        }

                        // Hide active tab
                        $(this).closest('div').find('#' + $active.data('tab')).addClass('hidden');
                        $active.removeClass('nav-tab-active');

                        // Show selected tab
                        $(this).closest('div').find('#' + tab).removeClass('hidden');
                        $(this).addClass('nav-tab-active');
                    });
                });
            </script>
		<?php
		endif;
	}
}
