<?php
/**
 * Main PHP file used to for initial calls to Pigeon Pack classes and functions.
 *
 * @package Pigeon Pack
 * @since 0.0.1
 */
 
/*
Plugin Name: Pigeon Pack
Plugin URI: http://pigeonpack.com/
Description: Free and easy email marketing, newsletters, and campaigns; built into your WordPress dashboard!
Author: layotte
Version: 1.0.3
Author URI: http://pigeonpack.com/
Tags: email, marketing, email marketing, newsletters, email newsletters, campaigns, email campaigns, widget, form, mailing lists
Special Thanks: 
Yusuke Kamiyamane - http://p.yusukekamiyamane.com/ - http://www.iconfinder.com/search/?q=iconset%3Afugue
Moini - http://openclipart.org/collection/collection-detail/Moini/7245
Minifiers:
http://www.minifyjs.com/javascript-compressor/
http://www.minifycss.com/css-compressor/
*/

//Define global variables...
define( 'PIGEON_PACK_VERSION' , '1.0.3' );
define( 'PIGEON_PACK_DB_VERSION', '1.0.0' );
define( 'PIGEON_PACK_API_URL', 'http://pigeonpack.com/api' );
define( 'PIGEON_PACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PIGEON_PACK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PIGEON_PACK_REL_DIR', dirname( plugin_basename( __FILE__ ) ) );

/**
 * Instantiate Pigeon Pack class, require helper files
 *
 * @since 0.0.1
 */
function pigeonpack_plugins_loaded() {

	require_once( PIGEON_PACK_PLUGIN_PATH . '/class.php' );

	// Instantiate the Pigeon Pack class
	if ( class_exists( 'PigeonPack' ) ) {
		
		global $dl_plugin_pigeonpack;
		
		$dl_plugin_pigeonpack = new PigeonPack();
		
		require_once( PIGEON_PACK_PLUGIN_PATH . '/functions.php' );
		require_once( PIGEON_PACK_PLUGIN_PATH . '/campaign-post-type.php' );
		require_once( PIGEON_PACK_PLUGIN_PATH . '/list-post-type.php' );
		require_once( PIGEON_PACK_PLUGIN_PATH . '/shortcodes.php' );
		require_once( PIGEON_PACK_PLUGIN_PATH . '/widgets.php' );
					
		$pigeonpack_shortcodes = new PigeonPack_Shortcodes();
			
		//Internationalization
		load_plugin_textdomain( 'pigeonpack', false, PIGEON_PACK_REL_DIR . '/i18n/' );
			
	}

}
add_action( 'plugins_loaded', 'pigeonpack_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init

/**
 * WordPress Activation Hook: does things that we need to keep track of during activation
 *
 * @since 1.0.1
 */
function pigeonpack_plugin_activation() {

	update_option( 'pigeonpack_flush_rewrite_rules', 'true' );
	
}
register_activation_hook( __FILE__, 'pigeonpack_plugin_activation' );

/**
 * Check to see if we need to flush the WordPress rewrite rules
 *
 * @since 1.0.1
 */
function pigeonpack_flush_rewrite_rules() {

	if ( 'true' === get_option( 'pigeonpack_flush_rewrite_rules' ) ) {

        // ATTENTION: This is *only* done during plugin activation hook in this example!
        // You should *NEVER EVER* do this on every page load!!
        flush_rewrite_rules();
        delete_option( 'pigeonpack_flush_rewrite_rules' );

    }
    
}
add_action( 'init', 'pigeonpack_flush_rewrite_rules', 4815162342 );
