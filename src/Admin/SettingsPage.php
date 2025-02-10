<?php
namespace AIWebsiteBuilder\Admin;

class SettingsPage {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            'Website Builder Settings',
            'AI Builder',
            'manage_options',
            'ai-website-builder',
            [$this, 'create_settings_page']
        );
    }

    public function register_settings() {
        register_setting('ai_builder_group', 'api_key'); // AI API Key
        register_setting('ai_builder_group', 'pixabay_api_key'); // Pixabay API Key
    }

    public function create_settings_page() {
        ?>
        <div class="wrap">
            <h1>AI Builder Settings</h1>

            <!-- Tabs for Basic Settings and API Settings -->
            <h2 class="nav-tab-wrapper">
                <a href="#api-settings" class="nav-tab nav-tab-active">API Settings</a>
            </h2>

            <form method="post" action="options.php">
                <?php settings_fields('ai_builder_group'); ?>
                <?php do_settings_sections('ai_builder_group'); ?>

                <!-- API Settings Tab -->
                <div id="api-settings" class="settings-section">
                    <h2>API Settings</h2>
                    <p class="description">Enter your API keys. Ensure they are correct to avoid errors.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">AI API Key</th>
                            <td>
                                <input type="password" name="api_key" class="regular-text" 
                                       value="<?php echo esc_attr(get_option('api_key')); ?>" />
                                <p class="description">
                                    Enter your AI API Secret Key. 
                                    <a href="https://aistudio.google.com/app/apikey" target="_blank">Get your AI API key</a>
                                </p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">Pixabay API Key</th>
                            <td>
                                <input type="password" name="pixabay_api_key" class="regular-text" 
                                       value="<?php echo esc_attr(get_option('pixabay_api_key')); ?>" />
                                <p class="description">
                                    Enter your Pixabay API key to fetch images.
                                    <a href="https://pixabay.com/api/docs/" target="_blank">Get your Pixabay API key</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
            // JavaScript to handle tab navigation
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('.nav-tab').forEach(tab => {
                    tab.addEventListener('click', function (e) {
                        e.preventDefault();

                        document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
                        tab.classList.add('nav-tab-active');

                        document.querySelectorAll('.settings-section').forEach(section => section.style.display = 'none');
                        document.querySelector(tab.getAttribute('href')).style.display = 'block';
                    });
                });

                // Show the first tab by default
                document.querySelector('.nav-tab-active').click();
            });
        </script>
        <?php
    }
}