<?php
/**
 * Plugin Name: AI Website Builder
 * Description: AI Website Builder.
 * Version: 1.0.0
 * Author: Rajan Vijayan
 * License: GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

// Autoload dependencies using Composer
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

if (!defined('AI_WEBSITE_BUILDER_PLUGIN_FILE')) {
    define('AI_WEBSITE_BUILDER_PLUGIN_FILE', __FILE__);
}

use AIWebsiteBuilder\Admin\SettingsPage;
use AIWebsiteBuilder\Modules\EnvironmentCheck;
use AIWebsiteBuilder\Modules\Builder;

// Initialize the plugin
function ai_website_builder_init() {
    // Load admin settings and logs page
    if ( is_admin() ) {
        new SettingsPage();
        new EnvironmentCheck();
        new Builder();
    }
}
add_action( 'plugins_loaded', 'ai_website_builder_init' );