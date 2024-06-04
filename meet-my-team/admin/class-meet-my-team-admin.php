<?php

// Include Files
include_once "includes/class-meet-my-team-build-cpt.php";

/**
 * Meet My Team
 *
 * @package   Meet My Team
 * @author    Aaron Lee <aaron.lee@buooy.com>
 * @license   GPL-2.0+
 * @link      http://buooy.com
 * @copyright 2014 Buooy
 */
class Meet_My_Team_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin            = Meet_My_Team::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		/* Build Custom Posts */
		$Meet_My_Team = new Meet_My_Team_Build_Cpt;

		// Build Custom Post Type
		add_action( 'init', array( $Meet_My_Team, 'build_custom_post_type' ), 0 );
		//add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Build the metaboxes
		add_action( 'init', array( $Meet_My_Team, 'cmb_initialize_mmt_meta_boxes' ), 9999 );
		add_filter( 'cmb_meta_boxes', array( $Meet_My_Team, 'mmt_metaboxes' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		//add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );


		//	add_filter( 'plugindonation_lib_strings_redirect-404-error-page-to-homepage-or-custom-page', array( $this, 'set_strings' ) );


	}

	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 * @since     1.0.0
	 *
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'edit.php?post_type=team_members',
			esc_html__( 'Support this Plugin', $this->plugin_slug ),
			esc_html__( 'Help this plugin', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug . '-settings',
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @return    null    Return early if no settings page is registered.
	 * @since     1.0.0
	 *
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), Meet_My_Team::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @return    null    Return early if no settings page is registered.
	 * @since     1.0.0
	 *
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), Meet_My_Team::VERSION );
		}

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		global $donation_obj;
		include_once 'views/admin.php';
		echo '<table class="form-table"><tbody>';
		$donation_obj->display();
		echo '</tbody></table>';
	}

}
