# Flux A.I. Image Generator for WordPress

# Generate ultra-realistic images directly within WordPress using the Flux A.I. API.
# Features

🎨 Generate AI images with customizable prompts
🖼️ Multiple style options (photorealistic, artistic, digital art)
📐 Support for various resolutions (1024x1024, 2048x2048, 4096x4096)
🗄️ Automatic integration with WordPress Media Library
🔒 Secure API key management
📝 Detailed logging for troubleshooting

# Requirements

PHP 7.4 or higher
WordPress 5.7 or higher
Composer for dependency management
Valid Flux A.I. API key

# Installation

Clone the repository into your WordPress plugins directory:

bashCopycd wp-content/plugins
git clone https://github.com/omoh128/ai-image-generator

Install dependencies using Composer:

bashCopycd flux-ai-image-generator
composer install

Activate the plugin through WordPress admin panel.
Configure your Flux A.I. API key in Settings → Flux A.I. Images.

# Configuration

Obtain your API key from Flux A.I. Dashboard
Navigate to WordPress Admin → Flux A.I. Images
Enter your API key and save settings

Usage
Using the Admin Interface

Go to Flux A.I. Images in your WordPress admin menu
Enter your image description in the prompt field
Select desired style and resolution
Click "Generate Image"
The generated image will be automatically added to your Media Library

Using PHP
phpCopy// Initialize the service
$flux_service = new FluxAIImageGenerator\Services\FluxAIService($api_key);

// Generate an image
$args = [
    'prompt' => 'A serene mountain landscape at sunset',
    'style' => 'photorealistic',
    'resolution' => '1024x1024'
];

$image_url = $flux_service->generateImage($args);
Development

# Project Structure

Copy ai-image-generator/
├── src/
│   ├── Admin/
│   │   └── SettingsPage.php
│   ├── Ajax/
│   │   └── ImageGenerationHandler.php
│   ├── Core/
│   │   └── Plugin.php
│   └── Services/
│       └── FluxAIService.php
├── vendor/
├── composer.json
├── flux-ai-image-generator.php
└── uninstall.php
Running Tests
bashCopycomposer test
Code Style
The project follows WordPress Coding Standards. To check your code:
bashCopycomposer lint
Logging
Logs are stored in wp-content/flux-ai-logs/generator.log
Troubleshooting
Common Issues

API Key Invalid

Verify your API key is correctly entered in the settings
Check if your API key has sufficient credits


Image Generation Failed

Check the logs for detailed error messages
Verify your server meets minimum requirements
Ensure your prompt follows Flux A.I. guidelines


Permission Issues

Verify WordPress file permissions
Ensure the web server can write to the uploads directory



Contributing

Fork the repository
Create your feature branch (git checkout -b feature/amazing-feature)
Commit your changes (git commit -m 'Add amazing feature')
Push to the branch (git push origin feature/amazing-feature)
Open a Pull Request

License
This project is licensed under the GPL v2 or later - see the LICENSE file for details.
Credits

Developed by [Your Name/Company]
Built with Flux A.I. API
Uses GuzzleHTTP for API requests
Uses Monolog for logging

Support
For support, please:

Check the documentation
Look through existing GitHub issues
Create a new issue if needed

Security
Please report any security issues to security@yourcompany.com rather than using the public issue tracker.