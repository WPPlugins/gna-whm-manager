<?php
/* 
 * Inits the admin dashboard side of things.
 * Main admin file which loads all settings panels and sets up admin menus. 
 */
if (!class_exists('GNA_WHM_Admin_Init')) {
	class GNA_WHM_Admin_Init {
		var $main_menu_page;
		var $settings_menu;

		public function __construct() {
			//This class is only initialized if is_admin() is true
			$this->admin_includes();
			add_action('admin_menu', array(&$this, 'create_admin_menus'));

			if ( isset($_GET['page']) && (strpos($_GET['page'], GNA_WHM_MENU_SLUG_PREFIX ) !== false) ) {
				add_action('admin_print_scripts', array(&$this, 'admin_menu_page_scripts'));
				add_action('admin_print_styles', array(&$this, 'admin_menu_page_styles'));
			}
		}

		public function admin_menu_page_scripts() {
			wp_enqueue_script('jquery');
			//wp_enqueue_script('gna-whm-script', GNA_WHM_URL. '/assets/js/gna-whm-manager.js', array(), GNA_WHM_VERSION);
			wp_register_script('gna-whm-script', GNA_WHM_URL. '/assets/js/gna-whm-manager.js', array(), GNA_WHM_VERSION);
			wp_localize_script('gna-whm-script', 'gna', array(
				's_whm_serverip_name' => __( 'WHM Server IP (or Hostname)', 'gna-whm-manager' ),
				's_whm_userid_name' => __( 'User ID', 'gna-whm-manager' ),
				's_whm_userpw_name' => __( 'User Password', 'gna-whm-manager' ),
				's_delete_btn' => __( 'Delete', 'gna-whm-manager' ),
			));
			wp_enqueue_script('gna-whm-script');
		}

		function admin_menu_page_styles() {
			wp_enqueue_style('gna-whm-manager-admin-css', GNA_WHM_URL. '/assets/css/gna-whm-manager.css');
		}

		public function admin_includes() {
			include_once('gna-whm-manager-admin-menu.php');
		}

		public function create_admin_menus() {
			$this->main_menu_page = add_menu_page( __('WHM Manager', 'gna-whm-manager'), __('WHM Manager', 'gna-whm-manager'), 'manage_options', 'gna-whm-settings-menu', array(&$this, 'handle_settings_menu_rendering'), GNA_WHM_URL . '/assets/images/gna_20x20.png' );

			add_submenu_page('gna-whm-settings-menu', __('Settings', 'gna-whm-manager'),  __('Settings', 'gna-whm-manager'), 'manage_options', 'gna-whm-settings-menu', array(&$this, 'handle_settings_menu_rendering'));

			add_action( 'admin_init', array(&$this, 'register_gna_whm_manager_settings') );
		}

		public function register_gna_whm_manager_settings() {
			register_setting( 'gna-whm-manager-setting-group', 'g_whm_configs' );
		}

		public function handle_settings_menu_rendering() {
			include_once('gna-whm-manager-admin-settings-menu.php');
			$this->settings_menu = new GNA_WHM_Settings_Menu();
		}
	}
}
