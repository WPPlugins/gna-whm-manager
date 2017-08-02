<?php
if (!class_exists('GNA_WHM')) {
	class GNA_WHM {
		var $plugin_url;
		var $admin_init;
		var $configs;

		public function init() {
			$class = __CLASS__;
			new $class;
		}

		public function __construct() {
			$this->load_configs();
			$this->define_constants();
			$this->define_variables();
			$this->includes();
			$this->loads();

			add_action( 'init', array(&$this, 'plugin_init'), 0 );
			add_action( 'add_meta_boxes', array(&$this, 'plugin_admin_init'), 0 );
			add_filter( 'plugin_row_meta', array(&$this, 'filter_plugin_meta'), 10, 2 );
		}

		public function load_configs() {
			include_once('inc/gna-whm-manager-config.php');
			$this->configs = GNA_WHM_Config::get_instance();
		}

		public function define_constants() {
			define('GNA_WHM_VERSION', '0.9.3');

			define('GNA_WHM_BASENAME', plugin_basename(__FILE__));
			define('GNA_WHM_URL', $this->plugin_url());

			define('GNA_WHM_MENU_SLUG_PREFIX', 'gna-whm-settings-menu');
		}

		public function define_variables() {
		}

		public function includes() {
			if(is_admin()) {
				include_once('admin/gna-whm-manager-admin-init.php');
			}
		}

		public function loads() {
			if(is_admin()){
				$this->admin_init = new GNA_WHM_Admin_Init();
			}
		}

		public function plugin_init() {
			// Set UI labels for Custom Post Type
			$labels = array(
				'name'                => _x( 'WHMs', 'Post Type General Name', 'gna-whm-manager' ),
				'singular_name'       => _x( 'WHM', 'Post Type Singular Name', 'gna-whm-manager' ),
				'menu_name'           => __( 'WHMs', 'gna-whm-manager' ),
				'parent_item_colon'   => __( 'Parent WHM', 'gna-whm-manager' ),
				'all_items'           => __( 'All cPanel', 'gna-whm-manager' ),
				'view_item'           => __( 'View cPanel', 'gna-whm-manager' ),
				'add_new_item'        => __( 'Add New cPanel', 'gna-whm-manager' ),
				'add_new'             => __( 'Add New', 'gna-whm-manager' ),
				'edit_item'           => __( 'Edit cPanel', 'gna-whm-manager' ),
				'update_item'         => __( 'Update cPanel', 'gna-whm-manager' ),
				'search_items'        => __( 'Search cPanel', 'gna-whm-manager' ),
				'not_found'           => __( 'Not Found', 'gna-whm-manager' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'gna-whm-manager' ),
			);
			
			// Set other options for Custom Post Type
			$args = array(
				'label'               => __( 'gna_whms', 'gna-whm-manager' ),
				'description'         => __( 'WHMs', 'gna-whm-manager' ),
				'labels'              => $labels,
				// Features this CPT supports in Post Editor
				'supports'            => array( 'title', 'revisions' ),
				// You can associate this CPT with a taxonomy or custom taxonomy. 
				'taxonomies'          => array(''),
				/* A hierarchical CPT is like Pages and can have
				* Parent and child items. A non-hierarchical CPT
				* is like Posts.
				*/
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
				'menu_icon'           => GNA_CURRENCY_CONVERTER_URL . '/assets/images/gna_20x20.png',
			);
			
			// Registering your Custom Post Type
			register_post_type( 'gna_whms', $args );

			load_plugin_textdomain('gna-whm-manager', false, dirname(plugin_basename(__FILE__ )) . '/languages/');
		}

		function plugin_admin_init() {
			add_meta_box(
				'cpanel_meta_box',
				'cPanel Details',
				array( $this, 'display_cpanel_meta_box' ),
				'gna_whms',
				'normal',
				'high'
			);
		}

		function display_cpanel_meta_box( $cpanel ) {
			$ex_rate_from_cur = esc_html( get_post_meta( $cpanel->ID, 'ex_rate_from_cur', true ) );
			$ex_rate_to_cur = esc_html( get_post_meta( $cpanel->ID, 'ex_rate_to_cur', true ) );
			$ex_rate_rate = esc_html( get_post_meta( $cpanel->ID, 'ex_rate_rate', true ) );
			$ex_rate_flag_from = esc_html( get_post_meta( $cpanel->ID, 'ex_rate_flag_from', true ) );
			wp_nonce_field('n_gna_cc_save_settings', 'n_gna_cc_save_meta');
			?>
			<table>
				<tr>
					<th style="width: 25%;">From Currency</th>
					<td style="width: 75%;"><input type="text" class="regular-text" name="ex_rate_from_cur" value="<?php echo $ex_rate_from_cur; ?>" /></td>
				</tr>
				<tr>
					<th>To Currency</th>
					<td><input type="text" class="regular-text" name="ex_rate_to_cur" value="<?php echo $ex_rate_to_cur; ?>" /></td>
				</tr>
				<tr>
					<th>Rate (From to To)</th>
					<td><input type="text" class="regular-text" name="ex_rate_rate" value="<?php echo $ex_rate_rate; ?>" /></td>
				</tr>
			</table>
			<?php
		}

		public function plugin_url() {
			if ($this->plugin_url) return $this->plugin_url;
			return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
		}

		public function filter_plugin_meta($links, $file) {
			if( strpos( GNA_WHM_BASENAME, str_replace('.php', '', $file) ) !== false ) { /* After other links */
				$links[] = '<a target="_blank" href="https://profiles.wordpress.org/chris_dev/" rel="external">' . __('Developer\'s Profile', 'gna-whm-manager') . '</a>';
			}

			return $links;
		}

		public function install() {
		}

		public function uninstall() {
		}

		public function activate_handler() {
			global $g_whm;
			$g_whm->configs->set_value('g_encryption_salt', self::generatePassword());
			$g_whm->configs->save_config();
		}

		public function deactivate_handler() {
			delete_option( 'g_whm_configs' );
		}
		
		public function generatePassword() {
			$alphabet = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
			$pass = array();
			$alphaLength = strlen($alphabet) - 1;
			for ($i = 0; $i < 15; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
			}
			return implode($pass);
		}
	}
}
$GLOBALS['g_whm'] = new GNA_WHM();
