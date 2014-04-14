<?php
/**
 * @package contactmix
 * @version 1.0
 */
/*
Plugin Name: ContactMix
Plugin URI: http://wordpress.org/plugins/contactmix
Description: ContactMix wordpress plugin will add one line ContactMix code in every wordpress page to increase your customers engagement. 
Author: contactmix
Version: 1.0
Author URI: http://contactmix.com
Text Domain: contactmix
*/

/*  
    Copyright 2012  contactmix  (email : thirdparty@contactmix.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


define('CONTACTMIX_PLUGIN_NAME', plugin_basename(__FILE__));
define('CONTACTMIX_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CONTACTMIX_PLUGIN_VERSION','1.0');

require_once 'classes/ContactMixCore.php';
require_once 'classes/settings.php';


if (!class_exists('ContactMix')) {

	class ContactMix{
		/**
		 * @var ContactMix
		 */
		static private $_instance = null;
		
		private $_contactmixcore;
		
		/**
		 * Get ContactMix object
		 *
		 * @return ContactMix
		 */
		static public function getInstance()
		{
			if (self::$_instance == null) {
				self::$_instance = new ContactMix();
			}

			return self::$_instance;
		}


		private function __construct()
		{

			register_activation_hook(CONTACTMIX_PLUGIN_NAME, array(&$this, 'pluginActivate'));
			register_deactivation_hook(CONTACTMIX_PLUGIN_NAME, array(&$this, 'pluginDeactivate'));
			register_uninstall_hook(CONTACTMIX_PLUGIN_NAME, array('contactmix', 'pluginUninstall'));

			
			## Register plugin widgets
			add_action('init', array($this, 'load_transl'));
			add_action('plugins_loaded', array(&$this, 'pluginLoad'));

			add_action( 'widgets_init', array(&$this, 'widgetsRegistration') );
			
			if (is_admin()) {
			add_action('wp_print_scripts', array(&$this, 'adminLoadScripts'));
			add_action('wp_print_styles', array(&$this, 'adminLoadStyles'));
			}
			else{

			add_action('wp_print_scripts', array(&$this, 'siteLoadScripts'));
			add_action('wp_print_styles', array(&$this, 'siteLoadStyles'));


			}

			add_action( 'wp_footer',array(&$this, 'footerScript'));

			$this->_contactmixcore = new ContactMixCore();
		}

		public function load_transl()
		{
			load_plugin_textdomain('contactmix', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}

		##
		## Loading Scripts and Styles
		##
	
		public function adminLoadStyles()
		{
            	  

		}
	
		public function adminLoadScripts(){
			
			wp_register_style('contactmix-admin-style',plugins_url('css/contactmix-admin.css', __FILE__));
			wp_enqueue_style( 'contactmix-admin-style' );
		  
		}
	
	
	
		public function siteLoadStyles(){
			
	
		}
	
	
		public function siteLoadScripts(){
			wp_enqueue_script( 'jquery' );			  
		}



		##
		## Widgets initializations
		##

		public function widgetsRegistration(){
		  		 
		 		 
		}


		
		##
		## Plugin Activation and Deactivation
		##

		/**
		* Activate plugin
		* @return void
		*/
		public function pluginActivate()
		{
			
			$settings_general = $this->_contactmixcore->getGeneralOptions();			
		 
		 	if(empty($settings_general['contactmix_toggle'])){
				$settings_general['contactmix_toggle'] = 'on';
			}
			
			update_option('contactmix_general_settings', $settings_general);

		}

		/**
		* Deactivate plugin
		* @return void
		*/
		public function pluginDeactivate(){
			
		}

		/**
		* Uninstall plugin
		* @return void
		*/
		static public function pluginUninstall()
		{

		}


		public function pluginLoad(){

		}
		
		public function footerScript(){
		  
		 $settings_general = $this->_contactmixcore->getGeneralOptions();
		 
		 
		 if(isset($settings_general['contactmix_toggle']) && $settings_general['contactmix_toggle'] == 'on'){
		  if( wp_script_is( 'jquery', 'done' ) ) {
		      ?>
			<!--Contact Mix Script -->
			<script type="text/javascript" src="//app.contactmix.com/remote/js/contactmix.min.js"></script>
		    
		    <?php		    
		  }
		}

		}	  

		}

}



//instantiate the class
if (class_exists('ContactMix')) {
	$ContactMix =  ContactMix::getInstance();
}
