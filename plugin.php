<?php

 /**
 * Plugin Name: WP Image Focal Point
 * Plugin URI: https://github.com/Denman-Digital/wp-image-focal-point
 * Update URI: gutestrap
 * Description: Set background focus position for media images.
 * Author: Denman Digital
 * Author URI: https://denman.digital/
 * Version: 1.1
 * Tested up to: 6.5
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-img-focal-point
 * Domain Path: /languages/
 * @package wp-image-focal-point
 */

namespace WP_Image_Focal_Point;

// Exit if accessed directly.
defined('ABSPATH') ||	exit;

if (!defined("WP_DEBUG")) {
	define("WP_DEBUG", false);
}
if (!defined("WP_DEBUG_LOG")) {
	define("WP_DEBUG_LOG", false);
}

define("WPIFP_PLUGIN_BASENAME", plugin_basename(__FILE__));
define("WPIFP_PLUGIN_FILE", basename(__FILE__));
define("WPIFP_PLUGIN_URI", plugin_dir_url(__FILE__));
define("WPIFP_PLUGIN_PATH", plugin_dir_path(__FILE__));

require_once plugin_dir_path(__FILE__) . 'src/init.php';

require_once plugin_dir_path(__FILE__) . 'update.php';

/**
 * Load plugin textdomain.
 */
function load_textdomain()
{
	load_plugin_textdomain('gutestrap', false, dirname(WPIFP_PLUGIN_BASENAME) . '/languages');
}
add_action('init', __NAMESPACE__ . '\load_textdomain');












