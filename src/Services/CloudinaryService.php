<?php

namespace Services;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Dotenv\Dotenv;

class CloudinaryService
{
    public function __construct()
    {
        // Load .env if not already loaded and CLOUDINARY_URL is missing
        // Load .env if not already loaded and CLOUDINARY_URL is missing
        $url = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? ($_SERVER['CLOUDINARY_URL'] ?? null));

        if (!$url) {
            // Adjust path to root directory assuming this file is in src/Services/
            $dotenvPath = __DIR__ . '/../../';
            if (file_exists($dotenvPath . '.env')) {
                $dotenv = Dotenv::createImmutable($dotenvPath);
                $dotenv->safeLoad();
                $url = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? ($_SERVER['CLOUDINARY_URL'] ?? null));
            }
        }

        // Configure Cloudinary explicitly from environment variable
        // The SDK might auto-pick it up, but explicit init ensures it's set
        if ($url) {
            Configuration::instance($url);
        }
    }

    public function upload($file)
    {
        // MOCK FOR CI/TESTING
        if (getenv('CI') || getenv('APP_ENV') === 'testing') {
            return 'https://res.cloudinary.com/demo/image/upload/v1/sample.jpg';
        }

        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new \Exception("Arquivo inválido para upload.");
        }

        try {
            $upload = new UploadApi();
            $result = $upload->upload($file['tmp_name'], [
                'folder' => 'loja_ponto_com/produtos',
                'resource_type' => 'auto'
            ]);

            return $result['secure_url'];
        } catch (\Exception $e) {
            // Re-throw to be handled by the controller
            throw new \Exception("Erro no upload para Cloudinary: " . $e->getMessage());
        }
    }

    public function delete($publicId)
    {
        try {
            $upload = new UploadApi();
            $result = $upload->destroy($publicId, [
                'resource_type' => 'image',
                'invalidate' => true
            ]);
            return $result;
        } catch (\Exception $e) {
            // Log warning but don't stop execution
            error_log("Erro ao deletar imagem do Cloudinary ($publicId): " . $e->getMessage());
            return null;
        }
    }
}
