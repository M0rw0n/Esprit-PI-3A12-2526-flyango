<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private array $rates = [];
    private ?string $baseCurrency = 'TND';

    public function __construct(?string $exchangeRateApiKey = null, ?HttpClientInterface $httpClient = null)
    {
        $this->apiKey = $exchangeRateApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->rates = [
            'TND' => 1,
            'EUR' => 0.31,
            'USD' => 0.33,
            'GBP' => 0.26,
            'CHF' => 0.29,
            'CAD' => 0.45,
            'AUD' => 0.50,
            'JPY' => 48.50,
            'MAD' => 3.30,
            'DZD' => 44.50,
            'LYD' => 1.58,
            'EGP' => 10.20,
            'SAR' => 1.23,
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_exchange_rate_api_key';
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;

        $fromRate = $this->getRate($from);
        $toRate = $this->getRate($to);

        if ($fromRate === 0) return $amount;

        return round(($amount / $fromRate) * $toRate, 2);
    }

    public function getRate(string $currency): float
    {
        return $this->rates[$currency] ?? 1;
    }

    public function getAllRates(): array
    {
        return $this->rates;
    }

    public function getFormattedAmount(float $amount, string $currency, ?string $displayCurrency = null): string
    {
        $converted = $displayCurrency ? $this->convert($amount, $currency, $displayCurrency) : $amount;
        $symbol = $this->getSymbol($displayCurrency ?? $currency);
        
        return $symbol . number_format($converted, 3, ',', ' ');
    }

    public function getSymbol(string $currency): string
    {
        $symbols = [
            'TND' => 'TND ',
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'CHF' => 'CHF ',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'JPY' => '¥',
            'MAD' => 'MAD ',
            'DZD' => 'DA ',
            'EGP' => 'E£ ',
            'SAR' => 'SAR ',
        ];
        
        return $symbols[$currency] ?? $currency . ' ';
    }

    public function refreshRates(): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                "https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/TND"
            );
            $data = $response->toArray();

            if (isset($data['conversion_rates'])) {
                $this->rates = array_merge(['TND' => 1], $data['conversion_rates']);
                return true;
            }
        } catch (\Exception $e) {
            // Use default rates
        }

        return false;
    }

    public function getRevenueInMultipleCurrencies(float $revenueTND): array
    {
        $currencies = ['TND', 'EUR', 'USD', 'GBP'];
        $result = [];

        foreach ($currencies as $currency) {
            $result[$currency] = [
                'amount' => $this->convert($revenueTND, 'TND', $currency),
                'symbol' => $this->getSymbol($currency),
                'formatted' => $this->getFormattedAmount($revenueTND, 'TND', $currency)
            ];
        }

        return $result;
    }
}