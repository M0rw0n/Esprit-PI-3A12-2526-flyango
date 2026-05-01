<?php

namespace App\Service;

use Symfony\Component\HttpClient\NativeHttpClient;

class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private ?NativeHttpClient $client = null;

    public function __construct(
        string $cloudName = '',
        string $apiKey = '',
        string $apiSecret = ''
    ) {
        $this->cloudName = $cloudName ?: $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '';
        $this->apiKey = $apiKey ?: $_ENV['CLOUDINARY_API_KEY'] ?? '';
        $this->apiSecret = $apiSecret ?: $_ENV['CLOUDINARY_API_SECRET'] ?? '';
    }

    private function getClient(): NativeHttpClient
    {
        if ($this->client === null) {
            $this->client = new NativeHttpClient();
        }
        return $this->client;
    }

    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && !empty($this->apiKey) && !empty($this->apiSecret);
    }

    public function upload360(string $filePath, string $folder = '360'): ?array
    {
        if (!$this->isConfigured()) {
            return $this->uploadLocal($filePath, $folder);
        }

        try {
            $timestamp = time();
            $signature = $this->generateSignature($timestamp, $folder);

            $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

            $client = $this->getClient();
            $response = $client->request('POST', $url, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath),
                    ],
                    [
                        'name' => 'timestamp',
                        'contents' => (string)$timestamp,
                    ],
                    [
                        'name' => 'api_key',
                        'contents' => $this->apiKey,
                    ],
                    [
                        'name' => 'signature',
                        'contents' => $signature,
                    ],
                    [
                        'name' => 'folder',
                        'contents' => $folder,
                    ],
                ],
            ]);

            $data = json_decode($response->getContent(), true);

            if (isset($data['secure_url'])) {
                return [
                    'url' => $data['secure_url'],
                    'public_id' => $data['public_id'],
                ];
            }
        } catch (\Exception $e) {
            error_log('Cloudinary upload failed: ' . $e->getMessage());
        }

        return $this->uploadLocal($filePath, $folder);
    }

    private function generateSignature(int $timestamp, string $folder): string
    {
        $params = "folder={$folder}&timestamp={$timestamp}{$this->apiSecret}";
        return hash('sha1', $params);
    }

    private function uploadLocal(string $filePath, string $folder): ?array
    {
        $dir = dirname(__DIR__, 2) . '/public/uploads/' . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = $folder . '_' . uniqid() . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        $dest = $dir . '/' . $filename;

        if (copy($filePath, $dest)) {
            return [
                'url' => '/uploads/' . $folder . '/' . $filename,
                'public_id' => $filename,
            ];
        }

        return null;
    }

    public function delete(string $publicId): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $timestamp = time();
            $signature = hash('sha1', "public_id={$publicId}&timestamp={$timestamp}{$this->apiSecret}");

            $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";

            $client = $this->getClient();
            $response = $client->request('POST', $url, [
                'multipart' => [
                    ['name' => 'public_id', 'contents' => $publicId],
                    ['name' => 'timestamp', 'contents' => (string)$timestamp],
                    ['name' => 'api_key', 'contents' => $this->apiKey],
                    ['name' => 'signature', 'contents' => $signature],
                ],
            ]);

            $data = json_decode($response->getContent(), true);
            return isset($data['result']) && $data['result'] === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }
}