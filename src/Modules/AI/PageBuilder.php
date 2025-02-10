<?php

namespace AIWebsiteBuilder\Modules\AI;

use AIWebsiteBuilder\Helper\Utils;
use AIEngine\AIEngine;

use Pixabay;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PageBuilder {
    
    public function __construct() {
        // Initialize any hooks or filters if needed
    }

    public static function generate_pages() {

        $api_key = get_option('api_key', '');
        $pixabay_api_key = get_option('pixabay_api_key', '');

        if (empty($api_key) || empty($pixabay_api_key)) {
            return false; // Return false if API key is not set
        }
        $ai_client = new AIEngine($api_key);

        $pixabayClient = new \Pixabay\PixabayClient([
            'key' => $pixabay_api_key
        ]);

        $sitemap = Utils::getTransient('ai_sitemap_json');
        $sitename = Utils::getTransient('ai_site_name');
        $category = Utils::getTransient('ai_category');
        $description = Utils::getTransient('ai_description');

        $json = json_decode($sitemap, true);

        $menus = $json['Menu'];
        

        foreach ($menus as $menu) {
            $menu_name = $menu['Name'];
            $sections = $menu['Section'];

            $images = $pixabayClient->getImages(['q' => $category, 'per_page' => 10], true);
            error_log(print_r($images['hits'], true));

            $image_url = '';
            foreach( $images['hits'] as $image ) {
                $image_url .= $image['webformatURL'] . ',';
            }


            // Craete a prompt for each menu to create Gutenberg blocks
            $prompt = 'Create a page for '.$menu_name.' menu with following sections it shold be Gutenberg blocks: ';
            foreach ($sections as $section) {
                $prompt .= $section['Name'].' , ';
            }
            $prompt .= 'for '.$sitename.' website. And this is site category: '.$category.'. This is business description '.$description.'.';
            $prompt .= 'Use randomly any 3 images from following '.$image_url.' images and relevant content for each section. It should be HTML format';

            error_log($prompt);

            // Generate content for the menu
            $content = $ai_client->generateContent($prompt);

            $html = Utils::cleanHTML($content);

            // Create a new page
            $page = array(
                'post_title' => $menu_name,
                'post_content' => $html,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
            );

            // Insert the page into the database
            $page_id = wp_insert_post($page);

            // Add the page to the menu
            $menu_id = wp_create_nav_menu($menu_name);
            $menu_item = array(
                'menu-item-object-id' => $page_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish'
            );

            wp_update_nav_menu_item($menu_id, 0, $menu_item);

        }
        return "Pages Generated";
    }
}