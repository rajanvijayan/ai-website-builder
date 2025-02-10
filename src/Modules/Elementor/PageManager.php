<?php

namespace AIWebsiteBuilder\Modules\Elementor;

class PageManager {

    /**
     * Create an Elementor page and assign sections.
     *
     * @param string $page_title Page title.
     * @param array  $sections Array of Elementor sections.
     * @return int|WP_Error Page post ID or error.
     */
    public static function createPage($page_title, $sections = []) {
        $post_id = wp_insert_post([
            'post_title'   => $page_title,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '', // Elementor content in post meta
        ]);

        if (is_wp_error($post_id)) {
            return $post_id; // Return error if insertion failed
        }

        // Save Elementor data to meta
        update_post_meta($post_id, '_elementor_data', wp_slash(json_encode($sections)));
        update_post_meta($post_id, '_elementor_edit_mode', 'builder');
        update_post_meta($post_id, '_elementor_version', ELEMENTOR_VERSION);
        update_post_meta($post_id, '_wp_page_template', 'elementor_full_width');

        return $post_id;
    }
}