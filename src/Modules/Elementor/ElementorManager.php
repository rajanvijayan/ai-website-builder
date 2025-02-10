<?php

namespace AIWebsiteBuilder\Modules\Elementor;

use AIWebsiteBuilder\Modules\Elementor\SectionManager;
use AIWebsiteBuilder\Modules\Elementor\PageManager;

class ElementorManager {
    
    public static function init() {
        // Ensure Elementor is active
        if (!class_exists('\Elementor\Plugin')) {
            return new \WP_Error('elementor_not_active', 'Elementor is not active.');
        }
    }

    /**
     * Save a new Elementor section.
     */
    public static function saveSection($section_data, $section_title = 'Custom Section') {
        return SectionManager::createSection($section_data, $section_title);
    }

    /**
     * Create a new Elementor page with sections.
     */
    public static function createPage($page_title, $sections = []) {
        return PageManager::createPage($page_title, $sections);
    }
}