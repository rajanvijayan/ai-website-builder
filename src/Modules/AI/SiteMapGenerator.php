<?php

namespace AIWebsiteBuilder\Modules\AI;

use AIWebsiteBuilder\Helper\Utils;
use AIEngine\AIEngine;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SiteMapGenerator {
    
    public function __construct() {
        // Register transient for ai_sitemap 
        add_action('init', [$this, 'register_sitemap_transient']);
    }

    public function register_sitemap_transient() {
        if (false === get_transient('ai_sitemap')) {
            $sitemap = $this->generate_sitemap();
            set_transient('ai_sitemap', $sitemap, 24 * HOUR_IN_SECONDS);
        }
    }

    public static function generate_sitemap() {
        // Get transient values 
        $sitename = get_transient('ai_site_name');
        $category = get_transient('ai_category');
        $description = get_transient('ai_description');

        // Generate sitemap if ai_sitemap_json transient is not set
        if (false === get_transient('ai_sitemap_json')) {
            self::prepare_sitemap($sitename, $category, $description);
        }

        return Utils::getTransient('ai_sitemap_json');
    }    

    public static function prepare_sitemap($sitename, $category, $description) {
        $api_key = get_option('api_key', '');
        if (empty($api_key)) {
            return false; // Return false if API key is not set
        }

        // Initialize AI engine with API key
        $ai_client = new AIEngine($api_key);

        // Prepare prompt 
        $prompt = 'Generate JSON for '.$sitename.' website with '.$category.' category and '.$description.' description. It should be a JSON file for following format and must atleast contain 5 menus and 3-4 sections in each menu.
        {
            Website: "Website Name",
            Menu: [
                {
                    Name: "Menu 1",
                    Section: [
                        {
                            Name: "Elementor Section",
                        },
                        {
                            Name: "Elementor Section",
                        },
                    ]
                },
                {
                    Name: "Menu 2",
                    Section: [
                        {
                            Name: "Elementor Section",
                        },
                        {
                            Name: "Elementor Section",
                        },
                    ]
                },
            ]
        }';

        $response_data = $ai_client->generateContent($prompt);

        $json = Utils::cleanJSON($response_data);

        if (json_decode($json)) {
            Utils::setTransient('ai_sitemap_json', $json, 24 * HOUR_IN_SECONDS);
            return true;
        }

        return false;
    }
}