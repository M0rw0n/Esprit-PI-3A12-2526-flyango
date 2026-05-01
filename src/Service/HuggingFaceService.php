<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HuggingFaceService
{
    private string $apiKey;
    private HttpClientInterface $client;
    private ?CacheInterface $cache;
    private string $model = 'sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2';

    public function __construct(
        string $huggingfaceApiKey,
        HttpClientInterface $client,
        ?CacheInterface $cache = null
    ) {
        $this->apiKey = trim($huggingfaceApiKey);
        $this->client = $client;
        $this->cache = $cache;
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && !str_starts_with($this->apiKey, 'your_');
    }

    public function generateEmbedding(string $text): ?array
    {
        if (!$this->isEnabled()) return null;

        try {
            $cacheKey = 'hf_embedding_' . md5(mb_strtolower(trim($text)));
            if ($this->cache) {
                return $this->cache->get($cacheKey, function (ItemInterface $item) use ($text) {
                    $item->expiresAfter(3600 * 24 * 30); // 30 days
                    return $this->callApi($text);
                });
            }
            return $this->callApi($text);
        } catch (\Exception $e) {
            error_log("HuggingFace Error: " . $e->getMessage());
            return null;
        }
    }

    private function callApi(string $text): ?array
    {
        // error_log("HF Debug: Using key starting with: " . substr($this->apiKey, 0, 5));
        $url = 'https://api-inference.huggingface.co/models/' . $this->model;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->apiKey,
            "Content-Type: application/json",
            "X-Wait-For-Model: true"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'inputs' => $text,
            'options' => ['wait_for_model' => true]
        ]));

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($result === false) {
            throw new \Exception("HF Request Error (curl): " . $error);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($result, true);
            $msg = $errorData['error'] ?? 'Inconnue';
            // Check if it's an array of errors
            if (is_array($msg)) $msg = json_encode($msg);
            
            if (str_contains($result, '<!DOCTYPE html>')) {
                throw new \Exception("HF API Error $httpCode: Server returned HTML (possible wrong URL or model).");
            }
            
            throw new \Exception("HF API Error $httpCode: $msg");
        }

        $data = json_decode($result, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("HF JSON Decode Error: " . json_last_error_msg());
        }

        // Case 1: [[0.1, 0.2, ...]] (Common for feature-extraction)
        if (isset($data[0]) && is_array($data[0])) {
            return $data[0];
        }
        
        // Case 2: [0.1, 0.2, ...] (Already flat)
        if (isset($data[0]) && (is_numeric($data[0]) || is_float($data[0]))) {
            return $data;
        }

        // Case 3: Nested but deeper? Just in case
        if (is_array($data)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));
            $flat = [];
            foreach ($iterator as $value) {
                if (is_numeric($value)) $flat[] = $value;
            }
            return !empty($flat) ? $flat : null;
        }

        return null;
    }
}
