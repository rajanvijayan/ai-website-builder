<?php

namespace AIWebsiteBuilder\Modules;

use AIWebsiteBuilder\Helper\Utils;
use AIWebsiteBuilder\Modules\AI\SiteMapGenerator;
use AIWebsiteBuilder\Modules\AI\PageBuilder;
use AIWebsiteBuilder\Modules\AI\SettingsBuilder;
use AIWebsiteBuilder\Modules\AI\FinalStep;

if (!defined('ABSPATH')) {
    exit;
}

class Builder {
    private $option_name = 'ai_site_status';

    public function __construct() {
        // if option not defiened create new option 
        if (!get_option($this->option_name)) {
            update_option($this->option_name, 'initiated');
        }
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_footer', [$this, 'show_popup']);
        add_action('wp_ajax_prefix_site_submission', [$this, 'handle_ajax_submission']);

        add_action('wp_ajax_generate_sitemap', [$this, 'generate_sitemap']);
        add_action('wp_ajax_generate_pages', [$this, 'generate_pages']);
        add_action('wp_ajax_basic_setup', [$this, 'basic_setup']);
        add_action('wp_ajax_final_setup', [$this, 'final_setup']);
        add_action('wp_ajax_site_ready', [$this, 'site_ready']);
    }

    public function set_initial_status() {
        if (!get_option($this->option_name)) {
            update_option($this->option_name, 'initiated');
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script('builder-popup', plugins_url('../assets/js/builder-popup.js', __FILE__), ['jquery'], '1.0', true);
        wp_localize_script('builder-popup', 'builder_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('builder_nonce')
        ]);

        wp_enqueue_style('builder-popup', plugins_url('../assets/css/builder-popup.css', __FILE__));
    }

    public function generate_sitemap() {
        $sitemap = SiteMapGenerator::generate_sitemap();
        if ($sitemap) {
            wp_send_json_success(['message' => 'Sitemap generated']);
        } else {
            wp_send_json_error(['message' => 'Error generating sitemap']);
        }
    }

    public function generate_pages() {
        $message = PageBuilder::generate_pages();
        wp_send_json_success(['message' => $message]);
    }

    public function basic_setup() {
        sleep(5);
        $message = SettingsBuilder::apply_basic_settings();
        wp_send_json_success(['message' => $message]);
    }

    public function final_setup() {
        sleep(5);
        $message = FinalStep::complete_setup();
        wp_send_json_success(['message' => $message]);
    }

    public function site_ready() {
        sleep(5);
        update_option($this->option_name, 'completed');
        wp_send_json_success(['message' => 'Site is ready']);
    }

    public function show_popup() {
        if (get_option($this->option_name) !== 'initiated') {
            return;
        }
        ?>
        <div id="builder-popup" class="builder-popup-overlay">
            <div class="builder-popup">
                <h2>Build My Website</h2>
                <form id="builder-form">
                    <label>Business Name:</label>
                    <input type="text" name="site_name" value="" required>
                    <label>Business Category:</label>
                    <select name="category" required>
                        <option value="construction">Construction</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="cafe">Cafe</option>
                        <option value="gym">Gym</option>
                        <option value="salon">Salon</option>
                        <option value="spa">Spa</option>
                        <option value="bakery">Bakery</option>
                        <option value="boutique">Boutique</option>
                        <option value="car-rental">Car Rental</option>
                        <option value="cleaning-services">Cleaning Services</option>
                        <option value="consulting">Consulting</option>
                        <option value="digital-marketing">Digital Marketing</option>
                        <option value="education">Education</option>
                        <option value="event-planning">Event Planning</option>
                        <option value="florist">Florist</option>
                        <option value="freelance">Freelance</option>
                        <option value="grocery-store">Grocery Store</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="home-maintenance">Home Maintenance</option>
                        <option value="it-services">IT Services</option>
                        <option value="landscaping">Landscaping</option>
                        <option value="legal-services">Legal Services</option>
                        <option value="pet-services">Pet Services</option>
                        <option value="photography">Photography</option>
                        <option value="real-estate">Real Estate</option>
                        <option value="retail-store">Retail Store</option>
                        <option value="tourism">Tourism</option>
                        <option value="transportation">Transportation</option>
                        <option value="travel-agency">Travel Agency</option>
                        <option value="wedding-services">Wedding Services</option>
                    </select>
                    <label>Description:</label>
                    <textarea name="description" required></textarea>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
        <div id="builder-progress" class="builder-progress-overlay" style="display: none;">
            <div class="builder-progress">
                <h3>Setting up your website...</h3>
                <ul>
                    <li id="step-1">Site Map Generated <span class="status">✔</span></li>
                    <li id="step-2">Pages Generated <span class="status">⏳</span></li>
                    <li id="step-3">Basic Setup Completed <span class="status">⏳</span></li>
                    <li id="step-4">Final Setup Completed <span class="status">⏳</span></li>
                    <li id="step-5">Site Ready <span class="status">⏳</span></li>
                </ul>
                <a href="<?php bloginfo('url');?>" id="visit-site-btn" style="display: none;" class="button button-primary">Visit Now</a>
            </div>
        </div>
        <style>
            
        </style>
        <?php
    }

    public function handle_ajax_submission() {
        check_ajax_referer('builder_nonce', 'nonce');

        $site_name = sanitize_text_field($_POST['site_name']);
        $category = sanitize_text_field($_POST['category']);
        $description = sanitize_textarea_field($_POST['description']);

        // save the data to the database as transient
        set_transient('ai_site_name', $site_name, 24 * HOUR_IN_SECONDS);
        set_transient('ai_category', $category, 24 * HOUR_IN_SECONDS);
        set_transient('ai_description', $description, 24 * HOUR_IN_SECONDS);

        // Process the data (For now, just updating the option)
        // update_option($this->option_name, 'processed');

        wp_send_json_success(['message' => $_POST]);
    }
}