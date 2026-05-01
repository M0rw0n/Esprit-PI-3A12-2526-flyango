<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentService
{
    private ?HttpClientInterface $client = null;
    private string $stripeSecretKey;
    private string $stripePublicKey;
    private string $paypalClientId;
    private string $paypalSecret;

    public function __construct(
        string $stripeSecretKey = '',
        string $stripePublicKey = '',
        string $paypalClientId = '',
        string $paypalSecret = ''
    ) {
        $this->stripeSecretKey = $stripeSecretKey ?: $_ENV['STRIPE_SECRET_KEY'] ?? '';
        $this->stripePublicKey = $stripePublicKey ?: $_ENV['STRIPE_PUBLIC_KEY'] ?? '';
        $this->paypalClientId = $paypalClientId ?: $_ENV['PAYPAL_CLIENT_ID'] ?? '';
        $this->paypalSecret = $paypalSecret ?: $_ENV['PAYPAL_SECRET'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getStripePublicKey(): string
    {
        return $this->stripePublicKey ?: 'pk_test_mock_key';
    }

    public function createStripePaymentIntent(float $amount, string $currency = 'eur', string $description = ''): array
    {
        if (empty($this->stripeSecretKey)) {
            return $this->getMockStripePayment($amount);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api.stripe.com/v1/payment_intents', [
                'auth_basic' => $this->stripeSecretKey . ':',
                'body' => [
                    'amount' => (int)($amount * 100),
                    'currency' => $currency,
                    'description' => $description,
                    'automatic_payment_methods[enabled]' => 'true'
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'client_secret' => $data['client_secret'] ?? '',
                'payment_intent_id' => $data['id'] ?? '',
                'status' => $data['status'] ?? ''
            ];
        } catch (\Exception $e) {
            return $this->getMockStripePayment($amount);
        }
    }

    public function verifyStripePayment(string $paymentIntentId): array
    {
        if (empty($this->stripeSecretKey)) {
            return ['success' => true, 'status' => 'succeeded'];
        }

        try {
            $response = $this->getClient()->request('GET', "https://api.stripe.com/v1/payment_intents/$paymentIntentId", [
                'auth_basic' => $this->stripeSecretKey . ':'
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'status' => $data['status'] ?? 'unknown',
                'amount' => ($data['amount'] ?? 0) / 100
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function createStripeCustomer(string $email, string $name): array
    {
        if (empty($this->stripeSecretKey)) {
            return ['success' => true, 'customer_id' => 'cus_mock_' . uniqid()];
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api.stripe.com/v1/customers', [
                'auth_basic' => $this->stripeSecretKey . ':',
                'body' => [
                    'email' => $email,
                    'name' => $name
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'customer_id' => $data['id'] ?? ''
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getPaypalAccessToken(): ?string
    {
        if (empty($this->paypalClientId) || empty($this->paypalSecret)) {
            return 'mock_paypal_token';
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'auth_basic' => $this->paypalClientId . ':' . $this->paypalSecret,
                'body' => ['grant_type' => 'client_credentials']
            ]);

            $data = $response->toArray();
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            return 'mock_paypal_token';
        }
    }

    public function createPaypalOrder(float $amount, string $currency = 'EUR', string $description = ''): array
    {
        $token = $this->getPaypalAccessToken();
        
        if (!$token || $token === 'mock_paypal_token') {
            return $this->getMockPaypalOrder($amount);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api-m.sandbox.paypal.com/v2/checkout/orders', [
                'headers' => ['Authorization' => "Bearer $token"],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => number_format($amount, 2, '.', '')
                        ],
                        'description' => $description
                    ]]
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'order_id' => $data['id'] ?? '',
                'status' => $data['status'] ?? '',
                'approve_url' => $data['links'][0]['href'] ?? ''
            ];
        } catch (\Exception $e) {
            return $this->getMockPaypalOrder($amount);
        }
    }

    public function capturePaypalOrder(string $orderId): array
    {
        $token = $this->getPaypalAccessToken();

        if (!$token || $token === 'mock_paypal_token') {
            return ['success' => true, 'status' => 'COMPLETED'];
        }

        try {
            $response = $this->getClient()->request('POST', "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId/capture", [
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'status' => $data['status'] ?? ''
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getMockStripePayment(float $amount): array
    {
        return [
            'success' => true,
            'client_secret' => 'pi_mock_' . uniqid() . '_secret_mock',
            'payment_intent_id' => 'pi_mock_' . uniqid(),
            'status' => 'requires_payment_method'
        ];
    }

    private function getMockPaypalOrder(float $amount): array
    {
        return [
            'success' => true,
            'order_id' => 'MOCK_ORDER_' . uniqid(),
            'status' => 'CREATED',
            'approve_url' => 'https://sandbox.paypal.com/checkoutnow?token=MOCK_TOKEN'
        ];
    }
}