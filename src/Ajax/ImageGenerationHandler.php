<?php
namespace AIImageGenerator\Ajax;

use AIImageGenerator\Services\FluxAIService;
use WP_Error;

/**
 * Handles AJAX image generation requests
 */
class ImageGenerationHandler {
    /**
     * Register AJAX actions
     */
    public function register() {
        add_action( 'wp_ajax_generate_image', [ $this, 'handle_image_generation' ] );
        add_action( 'wp_ajax_nopriv_generate_image', [ $this, 'handle_image_generation' ] );
    }

    /**
     * Handle image generation AJAX request
     */
    public function handle_image_generation() {
        // Verify nonce for security
        check_ajax_referer( 'ai_generate_image', 'nonce' );

        // Validate user permissions
        if ( ! current_user_can( 'upload_files' ) ) {
            wp_send_json_error( __( 'Insufficient permissions', 'ai-image-generator' ) );
        }

        // Sanitize input
        $prompt = sanitize_text_field( $_POST['prompt'] ?? '' );
        $style = sanitize_text_field( $_POST['style'] ?? 'photorealistic' );
        $resolution = sanitize_text_field( $_POST['resolution'] ?? '1024x1024' );

        // Validate inputs
        if ( empty( $prompt ) ) {
            wp_send_json_error( __( 'Prompt is required', 'ai-image-generator' ) );
        }

        // Retrieve API key
        $api_key = get_option( 'flux_ai_api_key' );
        if ( empty( $api_key ) ) {
            wp_send_json_error( __( 'Flux A.I. API key is not configured', 'flux-ai-image-generator' ) );
        }

        // Prepare generation arguments
        $generation_args = [
            'prompt'     => $prompt,
            'style'      => $style,
            'resolution' => $resolution,
            'num_images' => 1,
        ];

        // Initialize Flux A.I. service
        $flux_service = new FluxAIService( $api_key );
        $image_url = $flux_service->generate_image( $generation_args );

        if ( ! $image_url ) {
            wp_send_json_error( __( 'Failed to generate image', 'flux-ai-image-generator' ) );
        }

        // Upload image to WordPress media library
        $attachment_id = $this->upload_image_from_url( $image_url );

        if ( is_wp_error( $attachment_id ) ) {
            wp_send_json_error( $attachment_id->get_error_message() );
        }

        // Return image details
        wp_send_json_success([
            'image_id'  => $attachment_id,
            'image_url' => wp_get_attachment_image_src( $attachment_id, 'large' )[0],
        ]);
    }

    /**
     * Upload image from URL to WordPress media library
     *
     * @param string $image_url URL of the image to upload.
     * @return int|WP_Error Attachment ID or error
     */
    private function upload_image_from_url( string $image_url ) {
        // Download image
        $tmp = download_url( $image_url );

        if ( is_wp_error( $tmp ) ) {
            return $tmp;
        }

        // Prepare file for upload
        $file_array = [
            'name'     => basename( $image_url ),
            'tmp_name' => $tmp,
        ];

        // Upload image
        $attachment_id = media_handle_sideload( $file_array );

        // Delete temporary file
        @unlink( $tmp );

        return $attachment_id;
    }
}