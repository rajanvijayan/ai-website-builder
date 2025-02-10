<?php

namespace AIWebsiteBuilder\Modules\Elementor;

class SectionManager {

    /**
     * Create and save an Elementor section.
     *
     * @param array  $section_data JSON array of section data.
     * @param string $section_title Section title.
     * @return int|WP_Error Section post ID or error.
     */
    public static function createSection($section_data, $section_title = 'Custom Section') {
        $post_id = wp_insert_post([
            'post_title'   => $section_title,
            'post_status'  => 'publish',
            'post_type'    => 'elementor_library',
            'post_content' => '', // Elementor content is stored in meta
            'post_excerpt' => 'Saved via AI Website Builder',
        ]);

        if (is_wp_error($post_id)) {
            return $post_id; // Return error if insertion failed
        }

        error_log($section_data);

        // Save section data
        update_post_meta($post_id, '_elementor_data', $section_data);
        update_post_meta($post_id, '_elementor_edit_mode', 'builder');
        update_post_meta($post_id, '_elementor_version', ELEMENTOR_VERSION);
        update_post_meta($post_id, '_wp_page_template', 'elementor_canvas');

        return $post_id;
    }
}