<?php
if (!class_exists('GNA_WHM_Settings_Menu')) {
	class GNA_WHM_Settings_Menu extends GNA_WHM_Admin_Menu {
		var $menu_page_slug = 'gna-whm-settings-menu';

		/* Specify all the tabs of this menu in the following array */
		var $menu_tabs;

		var $menu_tabs_handler = array(
			'tab1' => 'render_tab1', 
			);

		public function __construct() {
			include_once(plugin_dir_path(__FILE__).'../inc/gna-whm-cipher.php');
			include_once(plugin_dir_path(__FILE__).'../libs/xmlapi.php');

			$this->render_menu_page();
		}

		public function set_menu_tabs() {
			$this->menu_tabs = array(
				'tab1' => __('General Settings', 'gna-whm-manager'),
			);
		}

		public function get_current_tab() {
			$tab_keys = array_keys($this->menu_tabs);
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $tab_keys[0];
			return $tab;
		}

		/*
		 * Renders our tabs of this menu as nav items
		 */
		public function render_menu_tabs() {
			$current_tab = $this->get_current_tab();

			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->menu_tabs as $tab_key => $tab_caption ) 
			{
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}

		/*
		 * The menu rendering goes here
		 */
		public function render_menu_page() {
			echo '<div class="wrap">';
			echo '<h2>'.__('Settings','gna-whm-manager').'</h2>';//Interface title
			$this->set_menu_tabs();
			$tab = $this->get_current_tab();
			$this->render_menu_tabs();
			?>
			<div id="poststuff"><div id="post-body">
			<?php 
				//$tab_keys = array_keys($this->menu_tabs);
				call_user_func(array(&$this, $this->menu_tabs_handler[$tab]));
			?>
			</div></div>
			</div><!-- end of wrap -->
			<?php
		}

		public function render_tab1() {
			global $g_whm;
			if ( isset($_POST['gna_whm_save_settings']) ) {
				$nonce = $_REQUEST['_wpnonce'];
				if ( !wp_verify_nonce($nonce, 'n_gna-whm-save-settings') ) {
					die("Nonce check failed on save settings!");
				}
				/*
				$xmlapi = new xmlapi($_POST['g_whm_serverip']);

				$xmlapi->set_output('xml');
				$xmlapi->password_auth($_POST['g_whm_userid'], $_POST['g_whm_userpw']);
				$xmlapi->set_debug(1);

				pprint_r($xmlapi->listaccts());
				*/
				$c = new GNA_WHM_Cipher($g_whm->configs->get_value('g_encryption_salt'));
				
				$g_whm->configs->set_value('g_whm_serverip', isset($_POST["g_whm_serverip"]) ? $c->encrypt($_POST["g_whm_serverip"]) : '');
				$g_whm->configs->set_value('g_whm_userid', isset($_POST["g_whm_userid"]) ? $c->encrypt($_POST["g_whm_userid"]) : '');
				$g_whm->configs->set_value('g_whm_userpw', isset($_POST["g_whm_userpw"]) ? $c->encrypt($_POST["g_whm_userpw"]) : '');
				$g_whm->configs->save_config();
				$this->show_msg_settings_updated();
			}
			?>
			<div class="postbox">
				<h3 class="hndle"><label for="title"><?php _e('GNA WHM Manager', 'gna-whm-manager'); ?></label></h3>
				<div class="inside">
					<p><?php _e('Thank you for using our GNA WHM Manager plugin.', 'gna-whm-manager'); ?></p>
				</div>
			</div> <!-- end postbox-->

			<div class="postbox">
				<h3 class="hndle"><label for="title"><?php _e('WHM Property', 'gna-whm-manager'); ?></label></h3>
				<form id="form_whm" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<?php wp_nonce_field('n_gna-whm-save-settings'); ?>
					<?php
						$c = new GNA_WHM_Cipher($g_whm->configs->get_value('g_encryption_salt'));
						
						$g_whm_serverips = $c->decrypt($g_whm->configs->get_value('g_whm_serverip'));
						$g_whm_userids = $c->decrypt($g_whm->configs->get_value('g_whm_userid'));
						$g_whm_userpws = $c->decrypt($g_whm->configs->get_value('g_whm_userpw'));

						//$g_whm_serverips = $g_whm->configs->get_value('g_whm_serverip');
						//$g_whm_userids = $g_whm->configs->get_value('g_whm_userid');
						//$g_whm_userpws = $g_whm->configs->get_value('g_whm_userpw');
pprint_r($g_whm_serverips);
						if ( isset($g_whm_serverips) && !empty($g_whm_serverips) ) {
							foreach ( $g_whm_serverips as $k => $g_whm_serverip ) {
					?>
					<div class="inside gna_grey_box">
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e('WHM Server IP (or Hostname)', 'gna-whm-manager')?>:</th>
								<td>
									<div class="input_fields_wrap">
										<input type="text" name="g_whm_serverip[]" class="g_whm_serverip regular-text" value="<?php echo $g_whm_serverips[$k]; ?>" required />
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('User ID', 'gna-whm-manager')?>:</th>
								<td>
									<div class="input_fields_wrap">
										<input type="text" name="g_whm_userid[]" class="g_whm_userid regular-text" required />
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('User Password', 'gna-whm-manager')?>:</th>
								<td>
									<div class="input_fields_wrap">
										<input type="password" name="g_whm_userpw[]" class="g_whm_userpw regular-text" required />
									</div>
								</td>
							</tr>
						</table>
					</div>
					<?php
							}
						}
					?>
					<div id="main_btn_wrapper">
						<button class="add_field_button button"><?php _e('Add More WHM', 'gna-whm-manager'); ?></button>
						<input type="submit" name="gna_whm_save_settings" value="<?php _e('Save Settings', 'gna-whm-manager')?>" class="button" />
					</div>
				</form>
			</div> <!-- end postbox-->
			<?php
		}
	} //end class
}
