<?php
namespace AIImageGenerator\Admin;

use AIImageGenerator\Services\FluxAIService;

/**
 * Handles admin settings page for Flux A.I. Image Generator
 */
class SettingsPage 
 {
    /**
     * Page slug
     */
    private const PAGE_SLUG = 'flux-ai-generator';

    /**
     * Option group
     */
    private const OPTION_GROUP = 'flux_ai_options';

    /**
     * Register settings and menu
     */
    public function register() {
        add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Add menu page to WordPress admin
     */
    public function add_menu_page() {
        add_menu_page(
            __( 'Flux A.I. Image Generator', 'flux-ai-image-generator' ),
            __( 'Flux A.I. Images', 'flux-ai-image-generator' ),
            'manage_options',
            self::PAGE_SLUG,
            [ $this, 'render_settings_page' ],
            'dashicons-images-alt2',
            99
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            self::OPTION_GROUP,
            'flux_ai_api_key',
            [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ]
        );

        add_settings_section(
            'flux_ai_settings_section',
            __( 'Flux A.I. Configuration', 'flux-ai-image-generator' ),
            [ $this, 'section_callback' ],
            self::PAGE_SLUG
        );

        add_settings_field(
            'flux_ai_api_key',
            __( 'API Key', 'flux-ai-image-generator' ),
            [ $this, 'api_key_callback' ],
            self::PAGE_SLUG,
            'flux_ai_settings_section'
        );
    }

    /**
     * Section description callback
     */
    public function section_callback() {
        echo '<p>' . esc_html__( 'Configure your Flux A.I. API settings.', 'flux-ai-image-generator' ) . '</p>';
    }

    /**
     * API Key input field callback
     */
    public function api_key_callback() {
        $api_key = get_option( 'flux_ai_api_key', '' );
        ?>
        <input 
            type="text" 
            name="flux_ai_api_key" 
            id="flux_ai_api_key" 
            value="<?php echo esc_attr( $api_key ); ?>" 
            class="regular-text"
            placeholder="<?php esc_attr_e( 'Enter your Flux A.I. API Key', 'flux-ai-image-generator' ); ?>"
        />
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( self::OPTION_GROUP );
                do_settings_sections( self::PAGE_SLUG );
                submit_button( __( 'Save Settings', 'flux-ai-image-generator' ) );
                ?>
            </form>
        </div>
        <?php
    }
}