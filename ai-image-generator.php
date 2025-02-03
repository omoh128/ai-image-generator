<?php
/**
 * A.I. Image Generator
 *
 * @package           AIImageGenerator
 * @author            Omomoh Agiogu
 * @copyright         2024 Your Company
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       A.I. Image Generator
 * Plugin URI:        https://github.com/omoh128/ai-image-generator
 * Description:       Generate ultra-realistic images using Flux A.I. within WordPress
 * Version:           1.0.0
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            Omomoh Agiogu
 * Author URI:        https://github.com/omoh128/ai-image-generator
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ai-image-generator
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Require Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Plugin initialization.
use AIImageGenerator\Core\Plugin;

// Bootstrap the plugin.
add_action( 'plugins_loaded', function() {
    $plugin = new Plugin();
    $plugin->init();
});