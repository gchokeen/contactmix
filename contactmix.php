<?php
/**
 * @package contactmix
 * @version 0.1
 */
/*
Plugin Name: Contact Mix
Plugin URI: http://www.example.com
Description: contactmix desc placeholder
Author: Gowri Sankar.R
Version: 0.1
Author URI: http://www.example.com
Text Domain: contactmix
*/

define('CONTACTMIX_PLUGIN_NAME', plugin_basename(__FILE__));
define('CONTACTMIX_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CONTACTMIX_PLUGIN_VERSION','0.1');

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