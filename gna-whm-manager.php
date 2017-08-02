<?php
/*
Plugin Name: GNA WHM Manager
Version: 0.9.3
Plugin URI: http://wordpress.org/plugins/gna-whm-manager/
Author: Chris Dev
Author URI: http://webgna.com/
Description: Easy to manage multiple WHM accounts similar to the WHMCS.
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gna-whm-manager
*/

if(!defined('ABSPATH')) exit; //Exit if accessed directly

include_once('gna-whm-manager-core.php');

register_activation_hook(__FILE__, array('GNA_WHM', 'activate_handler'));		//activation hook
register_deactivation_hook(__FILE__, array('GNA_WHM', 'deactivate_handler'));	//deactivation hook
