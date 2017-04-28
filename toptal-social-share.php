<?php
/**
 * Toptal Social Share Plugin
 *
 * @since             1.0.0
 * @package           Toptal_social_share
 *
 * @wordpress-plugin
 * Plugin Name:       Toptal Social Share
 * Description:       This plugin will automatically display selected social network(s) sharing buttons in posts and/or on pages.
 * Version:           1.0.0
 * Author:            Rodrigo Salles
 * Author URI:        http://rodrigosalles.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       toptal-social-share
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// Define our constants
define( 'TSS_SOCIAL_SHARE_VERSION', '1.0.0' );
define( 'TSS_SOCIAL_SHARE_SLUG', 'toptal-social-share' );
define( 'TSS_SOCIAL_SHARE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSS_SOCIAL_SHARE_URL', plugin_dir_url( __FILE__ ) );
define( 'TSS_SOCIAL_SHARE_BASENAME', plugin_basename( __FILE__ ) );

// Include the main plugin class.
require_once TSS_SOCIAL_SHARE_PATH . 'classes/class-toptal-social-share.php';

/**
 * Set the default values on plugin activation.
 *
 * @since  1.0.0
 */
register_activation_hook( __FILE__, array( 'Toptal_Social_Share', 'set_default_values' ) );

/**
 * Initialize the plugin.
 *
 * @since  1.0.0
 */
function tss_init() {

	$tss = new Toptal_Social_Share();
	$tss->initialize();
}

tss_init();