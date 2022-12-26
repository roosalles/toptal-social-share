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

// Define our constants.
define( 'TSS_VERSION', '1.0.0' );
define( 'TSS_SLUG', 'toptal-social-share' );
define( 'TSS_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSS_URL', plugin_dir_url( __FILE__ ) );
define( 'TSS_BASENAME', plugin_basename( __FILE__ ) );

// Social Networks share urls.
define( 'TSS_FACEBOOK_URL', 'https://www.facebook.com/sharer.php' );
define( 'TSS_TWITTER_URL', 'https://twitter.com/intent/tweet' );
define( 'TSS_LINKEDIN_URL', 'https://www.linkedin.com/shareArticle' );
define( 'TSS_PINTEREST_URL', 'https://www.pinterest.com/pin/create/button/' );
define( 'TSS_WHATSAPP_URL', 'whatsapp://send' );

// Include the main plugin class.
require_once TSS_PATH . 'classes/class-toptal-social-share.php';

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
