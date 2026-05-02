<?php

namespace App\Controller;

use App\Service\Api\WeatherApiService;
use App\Service\Api\MapsApiService;
use App\Service\Api\FlightApiService;
use App\Service\Api\SkyscannerApiService;
use App\Service\Api\PlacesApiService;
use App\Service\Api\PaymentService;
use App\Service\Api\TranslationService;
use App\Service\Api\AiService;
use App\Service\Api\ModerationService;
use App\Service\Api\GeolocationService;
use App\Service\Api\ExchangeRateService;
use App\Service\Api\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api-explorer')]
class ApiExplorerController extends AbstractController
{
    #[Route('', name: 'api_explorer')]
    public function index(Request $request): Response
    {
        return $this->render('api/explorer.html.twig');
    }

    #[Route('/test', name: 'api_test')]
    public function test(
        Request $request,
        WeatherApiService $weather,
        FlightApiService $flights,
        SkyscannerApiService $skyscanner,
        PlacesApiService $places,
        TranslationService $translate,
        AiService $ai,
        ModerationService $moderation,
        GeolocationService $geo,
        ExchangeRateService $exchange,
        QrCodeService $qrcode,
        PaymentService $payment
    ): Response {
        $endpoint = $request->query->get('endpoint', 'weather');
        $result = ['success' => false, 'data' => []];
        $params = [];

        switch ($endpoint) {
            case 'weather':
                $city = $request->query->get('city', 'Paris');
                $result = $weather->getCurrentWeather($city);
                $params = ['city' => $city];
                break;

            case 'weather-forecast':
                $city = $request->query->get('city', 'Paris');
                $days = (int)$request->query->get('days', 5);
                $result = $weather->getForecast($city, $days);
                $params = ['city' => $city, 'days' => $days];
                break;

            case 'flights':
                $origin = $request->query->get('origin', 'PAR');
                $destination = $request->query->get('destination', 'LON');
                $date = $request->query->get('date', date('Y-m-d'));
                $result = $flights->searchFlights($origin, $destination, $date);
                $params = ['origin' => $origin, 'destination' => $destination, 'date' => $date];
                break;

            case 'places':
                $query = $request->query->get('query', 'restaurant Paris');
                $result = $places->searchText($query);
                $params = ['query' => $query];
                break;

            case 'translate':
                $text = $request->query->get('text', 'Hello world');
                $target = $request->query->get('target', 'fr');
                $result = $translate->translate($text, $target);
                $params = ['text' => $text, 'target' => $target];
                break;

            case 'ai-chat':
                $prompt = $request->query->get('prompt', 'What are the best places to visit in France?');
                $result = $ai->generateResponse($prompt);
                $params = ['prompt' => $prompt];
                break;

            case 'moderation':
                $text = $request->query->get('text', 'This is a great product!');
                $result = $moderation->checkToxicity($text);
                $params = ['text' => $text];
                break;

            case 'geolocation':
                $ip = $request->query->get('ip', '');
                $result = $geo->getLocationFromIp($ip);
                $params = ['ip' => $ip ?: 'auto'];
                break;

            case 'exchange':
                $from = $request->query->get('from', 'EUR');
                $to = $request->query->get('to', 'USD');
                $amount = (float)$request->query->get('amount', 100);
                $result = $exchange->convert($amount, $from, $to);
                $params = ['from' => $from, 'to' => $to, 'amount' => $amount];
                break;

            case 'qrcode':
                $content = $request->query->get('content', 'Fly&Go Travel');
                $qr = $qrcode->generateQrCode($content);
                $result = ['success' => true, 'qrcode' => $qr, 'content' => $content];
                $params = ['content' => $content];
                break;

            case 'payment-stripe':
                $amount = (float)$request->query->get('amount', 100);
                $result = $payment->createStripePaymentIntent($amount);
                $params = ['amount' => $amount];
                break;

            default:
                $result = ['success' => false, 'error' => 'Unknown endpoint'];
        }

        return $this->render('api/test.html.twig', [
            'endpoint' => $endpoint,
            'result' => $result,
            'params' => $params
        ]);
    }
}