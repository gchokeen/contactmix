<?php

class Settings_API_Tabs_ContactMix_Plugin {
	
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $contactmix_general_settings_key = 'contactmix_general_settings';	
	private $plugin_options_key = 'contactmix_plugin_options';
	private $plugin_settings_tabs = array();
	
	
	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 * current ones speak for themselves.
	 */
	function __construct() {
		
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_contactmix_general_settings' ) );		
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
		
		add_filter( 'plugin_action_links_'.CONTACTMIX_PLUGIN_NAME, array( &$this, 'pluginSettingsLink' ) );
		
		
		
	}
	
	/*
	 * Loads both the general and advanced settings from
	 * the database into their respective arrays. Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 *
	 * To get settings, use a new ContactMixCore class and
	 *  call getGeneralOptions and getAdvancedOptions from there
	 */
	function load_settings() {
		$this->contactmix_general_settings = (array) get_option( $this->contactmix_general_settings_key );
		
		// Merge with defaults
		//$this->contactmix_general_settings = array_merge( array(
		//	'general_option' => 'General value'
		//), $this->contactmix_general_settings );

	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_contactmix_general_settings() {
		$this->plugin_settings_tabs[$this->contactmix_general_settings_key] = __('General','contactmix');
		
		register_setting( $this->contactmix_general_settings_key, $this->contactmix_general_settings_key );
		add_settings_section( 'contactmix_section_general',__('ContactMix','contactmix'), array( &$this, 'contactmix_section_general_desc' ), $this->contactmix_general_settings_key );
		add_settings_field( 'contactmix_toggle',__('Turn ON/OFF Contact Mix','contactmix') , array( &$this, 'field_contactmix_toggle' ), $this->contactmix_general_settings_key, 'contactmix_section_general' );

	}

	
	
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function contactmix_section_general_desc() { }
	
	
	
	function field_contactmix_toggle() {
		$contactmix_toggle = (isset($this->contactmix_general_settings['contactmix_toggle'])?esc_attr( $this->contactmix_general_settings['contactmix_toggle'] ):'');
		?>
		
		<div class="onoffswitch">
		    <input type="checkbox" name="<?php echo $this->contactmix_general_settings_key; ?>[contactmix_toggle]" class="onoffswitch-checkbox" id="contactmix_toggle"  value="on" <?php checked($contactmix_toggle,'on');?> />
		    <label class="onoffswitch-label" for="contactmix_toggle">
			<div class="onoffswitch-inner"></div>
			<div class="onoffswitch-switch"></div>
		    </label>
		</div>
		
						
			
			<?php
	}		
	
	/*
	 * Called during admin_menu, adds an options
	 * page under Settings called My Settings, rendered
	 * using the plugin_options_page method.
	 */
	function add_admin_menus() {
		add_options_page(__('Contact Mix','contactmix'),__('Contact Mix','contactmix'), 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	}
	
	
	#
	# Plugin Settings link
	#

	public function pluginSettingsLink($links){
	   $settings_link = '<a href="options-general.php?page='.$this->plugin_options_key.'.php">'.__('Settings', 'contactmix').'</a>'; 
	   array_unshift($links, $settings_link); 
	  return $links; 
	}
	
	
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->contactmix_general_settings_key;
		?>
		<div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->contactmix_general_settings_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
};

// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$settings_api_tabs_contactmix_plugin = new Settings_API_Tabs_ContactMix_Plugin;' ) );