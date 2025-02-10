<?php

namespace AIWebsiteBuilder\Modules;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class EnvironmentCheck {
    
    public function __construct() {
        add_action('admin_init', [$this, 'check_environment']);
    }

    public function check_environment() {
        if (!is_admin()) {
            return;
        }

        // Check if Elementor is installed and active
        if (!self::is_plugin_active('elementor/elementor.php')) {
            add_action('admin_notices', [$this, 'show_elementor_missing_notice']);
        }
    }

    private static function is_plugin_active($plugin) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        return is_plugin_active($plugin);
    }

    public function show_elementor_missing_notice() {
        echo '<div class="notice notice-error"><p><strong>AI Website Builder:</strong> Elementor is not installed or activated. Please install/activate it for full functionality.</p></div>';
    }
}