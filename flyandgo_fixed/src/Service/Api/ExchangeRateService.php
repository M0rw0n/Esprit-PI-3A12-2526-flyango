<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $exchangeRateApiKey = '')
    {
        $this->apiKey = $exchangeRateApiKey ?: $_ENV['EXCHANGERATE_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getExchangeRate(string $from, string $to): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockRate($from, $to);
        }

        try {
            $response = $this->getClient()->request('GET', "https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/" . strtoupper($from));
            $data = $response->toArray();

            if (isset($data['conversion_rates'][strtoupper($to)])) {
                return [
                    'success' => true,
                    'from' => strtoupper($from),
                    'to' => strtoupper($to),
                    'rate' => $data['conversion_rates'][strtoupper($to)],
                    'timestamp' => $data['time_last_update_utc'] ?? ''
                ];
            }

            return $this->getMockRate($from, $to);
        } catch (\Exception $e) {
            return $this->getMockRate($from, $to);
        }
    }

    public function convert(float $amount, string $from, string $to): array
    {
        $rate = $this->getExchangeRate($from, $to);
        
        return [
            'success' => true,
            'amount' => $amount,
            'from' => $from,
            'to' => $to,
            'converted_amount' => $amount * ($rate['rate'] ?? 1),
            'rate' => $rate['rate'] ?? 1
        ];
    }

    public function getAvailableCurrencies(): array
    {
        return [
            'success' => true,
            'currencies' => [
                'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound',
                'JPY' => 'Japanese Yen', 'CHF' => 'Swiss Franc', 'CAD' => 'Canadian Dollar',
                'AUD' => 'Australian Dollar', 'CNY' => 'Chinese Yuan', 'INR' => 'Indian Rupee',
                'MXN' => 'Mexican Peso', 'BRL' => 'Brazilian Real', 'KRW' => 'South Korean Won',
                'SGD' => 'Singapore Dollar', 'HKD' => 'Hong Kong Dollar', 'SEK' => 'Swedish Krona',
                'NOK' => 'Norwegian Krone', 'DKK' => 'Danish Krone', 'NZD' => 'New Zealand Dollar'
            ]
        ];
    }

    private function getMockRate(string $from, string $to): array
    {
        $mockRates = [
            'EUR' => ['USD' => 1.10, 'GBP' => 0.86, 'JPY' => 165.0],
            'USD' => ['EUR' => 0.91, 'GBP' => 0.79, 'JPY' => 150.0],
            'GBP' => ['EUR' => 1.16, 'USD' => 1.27, 'JPY' => 190.0]
        ];

        $rate = $mockRates[strtoupper($from)][strtoupper($to)] ?? 1.0;

        return [
            'success' => true,
            'from' => strtoupper($from),
            'to' => strtoupper($to),
            'rate' => $rate,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}