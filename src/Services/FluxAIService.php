<?php
namespace AIImageGenerator\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;

/**
 * Service for interacting with Flux A.I. API
 */
class FluxAIService {
    /**
     * API endpoint
     *
     * @var string
     */
    private $endpoint;

    /**
     * API Key
     *
     * @var string
     */
    private $apiKey;

    /**
     * HTTP Client
     *
     * @var Client
     */
    private $client;

    /**
     * Logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param string $apiKey Flux A.I. API key.
     * @param string $endpoint API endpoint URL.
     */
    public function __construct(
        string $apiKey, 
        string $endpoint = 'https://api.bfl.ml/v1/flux-pro-1.1'
    ) {
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
        $this->client = new Client();
        $this->initLogger();
    }

    /**
     * Initialize logging
     */
    private function initLogger(): void {
        $this->logger = new Logger('flux_ai_generator');
        $logPath = WP_CONTENT_DIR . '/flux-ai-logs/generator.log';
        
        // Ensure log directory exists
        if (!is_dir(dirname($logPath))) {
            wp_mkdir_p(dirname($logPath));
        }

        $this->logger->pushHandler(
            new StreamHandler($logPath, Logger::INFO)
        );
    }

    /**
     * Generate image via Flux A.I.
     *
     * @param array $args Image generation parameters.
     * @return string|false Generated image URL or false on failure
     */
    public function generateImage(array $args) {
        try {
            $response = $this->client->post($this->endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $this->sanitizeArgs($args),
                'timeout' => 30  // 30-second timeout
            ]);

            $body = json_decode($response->getBody(), true);

            // Validate response
            if (empty($body['images'][0])) {
                $this->logger->warning('No image generated', $args);
                return false;
            }

            $this->logger->info('Image generated successfully', $args);
            return $body['images'][0];

        } catch (RequestException $e) {
            $this->logger->error('API Request Failed', [
                'message' => $e->getMessage(),
                'args'    => $args
            ]);
            return false;
        } catch (Exception $e) {
            $this->logger->critical('Unexpected error', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sanitize and validate generation arguments
     *
     * @param array $args Input arguments.
     * @return array Sanitized arguments
     */
    private function sanitizeArgs(array $args): array {
        $defaultArgs = [
            'prompt'     => '',
            'style'      => 'photorealistic',
            'resolution' => '1024x1024',
            'num_images' => 1
        ];

        $sanitizedArgs = wp_parse_args($args, $defaultArgs);

        // Validate and sanitize specific fields
        $sanitizedArgs['prompt'] = sanitize_text_field($sanitizedArgs['prompt']);
        $sanitizedArgs['style'] = in_array($sanitizedArgs['style'], 
            ['photorealistic', 'artistic', 'digital-art'], true
        ) ? $sanitizedArgs['style'] : 'photorealistic';

        // Validate resolution
        $validResolutions = ['1024x1024', '2048x2048', '4096x4096'];
        $sanitizedArgs['resolution'] = in_array($sanitizedArgs['resolution'], $validResolutions, true)
            ? $sanitizedArgs['resolution']
            : '1024x1024';

        // Limit number of images
        $sanitizedArgs['num_images'] = min(max(1, (int)$sanitizedArgs['num_images']), 4);

        return $sanitizedArgs;
    }
}

/**
 * Image Generation Helper Function
 */
function generateAndSaveImage($prompt, $width, $height, $apiKey, $apiUrl, $outputFolder) {
    // Ensure the output folder exists
    if (!is_dir($outputFolder)) {
        mkdir($outputFolder, 0777, true);
    }

    // Create the payload for the API request
    $payload = [
        'prompt' => $prompt,
        'width' => $width,
        'height' => $height,
        'prompt_upsampling' => false,
        'seed' => rand(0, 999999), // Optional seed for reproducibility
        'safety_tolerance' => 3
    ];

    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the API request
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        die('Error: Unable to connect to the API.');
    }

    // Decode the API response
    $data = json_decode($response, true);
    if (!isset($data['image_url'])) {
        die('Error: Invalid API response - ' . $response);
    }

    // Download the image
    $imageContent = file_get_contents($data['image_url']);
    if (!$imageContent) {
        die('Error: Unable to download the image.');
    }

    // Save the image to the output folder
    $outputFile = $outputFolder . '/generated_image_' . time() . '.png';
    if (!file_put_contents($outputFile, $imageContent)) {
        die('Error: Unable to save the image.');
    }

    echo "Image successfully saved to: $outputFile\n";
}
