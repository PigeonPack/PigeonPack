<?php
/**
 * Main PHP file used to for initial calls to Pigeon Pack classes and functions.
 *
 * @package Pigeon Pack
 * @since 0.0.1
 */
 
/*
Plugin Name: Pigeon Pack
Plugin URI: http://getpigeonpack.com/
Description: Easy and affordable email marketing, newsletters, and campaigns; built right in your WordPress dashboard, no third parties needed!
Author: layotte
Version: 0.0.1
Author URI: http://getpigeonpack.com/
Tags: email, marketing, email marketing, newsletters, email newsletters, campaigns, email campaigns, widget, form, mailing lists
Special Thanks: 
Yusuke Kamiyamane - http://p.yusukekamiyamane.com/ - http://www.iconfinder.com/search/?q=iconset%3Afugue
Turbomilk - http://graphicriver.net/free/web-icon-set/ - http://www.turbomilk.com/ - http://www.iconfinder.com/search/?q=iconset%3Awebset
*/

//Define global variables...
define( 'PIGEON_PACK_VERSION' , '0.0.1' );
define( 'PIGEON_PACK_DB_VERSION', '0.0.1' );
define( 'PIGEON_PACK_API_URL', 'http://getpigeonpack.com/api' );
define( 'PIGEON_PACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PIGEON_PACK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PIGEON_PACK_REL_DIR', dirname( plugin_basename( __FILE__ ) ) );

/**
 * Instantiate Pigeon Pack class, require helper files
 *
 * @since 0.0.1
 */
function pigeonpack_plugins_loaded() {

	require_once( 'pigeonpack-class.php' );

	// Instantiate the Pigeon Pack class
	if ( class_exists( 'PigeonPack' ) ) {
		
		global $dl_plugin_pigeonpack;
		
		$dl_plugin_pigeonpack = new PigeonPack();
		
		require_once( 'pigeonpack-functions.php' );
		require_once( 'pigeonpack-campaign-post-type.php' );
		require_once( 'pigeonpack-list-post-type.php' );
		require_once( 'pigeonpack-shortcodes.php' );
		require_once( 'pigeonpack-widgets.php' );
					
		$pigeonpack_shortcodes = new PigeonPack_Shortcodes();
			
		//Internationalization
		load_plugin_textdomain( 'pigeonpack', false, PIGEON_PACK_REL_DIR . '/i18n/' );
			
	}

}
add_action( 'plugins_loaded', 'pigeonpack_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init