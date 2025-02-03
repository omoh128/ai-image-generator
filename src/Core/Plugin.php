<?php
namespace AIImageGenerator\Core;

use AIImageGenerator\Admin\SettingsPage;
use AIImageGenerator\Ajax\ImageGenerationHandler;

/**
 * Core plugin class for initialization and setup
 */
class Plugin {
    /**
     * Initialize plugin components
     */
    public function init() {
        // Register admin settings
        $settings_page = new SettingsPage();
        $settings_page->register();

        // Setup AJAX handlers
        $ajax_handler = new ImageGenerationHandler();
        $ajax_handler->register();

        // Enqueue scripts and styles
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Enqueue plugin scripts and styles
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_scripts( $hook ) {
        // Only enqueue on plugin page.
        if ( 'toplevel_page_flux-ai-generator' !== $hook ) {
            return;
        }

        wp_enqueue_script(
            'flux-ai-generator',
            plugin_dir_url( __DIR__ ) . '../assets/js/generator.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );

        wp_localize_script(
            'flux-ai-generator',
            'fluxAjax',
            [
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'ajax_nonce' => wp_create_nonce( 'flux_ai_generate_image' ),
            ]
        );
    }
}