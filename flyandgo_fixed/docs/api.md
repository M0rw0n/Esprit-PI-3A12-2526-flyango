<?php
/**
 * API Documentation - Fly&Go
 * 
 * All endpoints are prefixed with /api
 */

return [
    // ============ WEATHER APIs ============
    'GET /api/weather' => [
        'description' => 'Get current weather for a city',
        'params' => ['city' => 'City name', 'units' => 'metric/imperial']
    ],
    'GET /api/weather/forecast' => [
        'description' => 'Get weather forecast',
        'params' => ['city' => 'City name', 'days' => 'Number of days (1-5)']
    ],

    // ============ MAPS & PLACES APIs ============
    'GET /api/maps/geocode' => [
        'description' => 'Get coordinates from address',
        'params' => ['address' => 'Address string']
    ],
    'GET /api/maps/directions' => [
        'description' => 'Get directions between two points',
        'params' => ['origin', 'destination', 'mode' => 'driving/walking/bicycling/transit']
    ],
    'GET /api/places/nearby' => [
        'description' => 'Search nearby places',
        'params' => ['lat', 'lng', 'type' => 'restaurant/museum/park', 'radius']
    ],
    'GET /api/places/search' => [
        'description' => 'Text search for places',
        'params' => ['query']
    },

    // ============ FLIGHT APIs ============
    'GET /api/flights/search' => [
        'description' => 'Search flights',
        'params' => ['origin', 'destination', 'date', 'return_date', 'adults']
    ],
    'GET /api/flights/airports' => [
        'description' => 'Search airports',
        'params' => ['keyword']
    ],
    'GET /api/prices/compare' => [
        'description' => 'Compare flight prices',
        'params' => ['origin', 'destination', 'date']
    ],

    // ============ QR CODE APIs ============
    'POST /api/qrcode/generate' => [
        'description' => 'Generate QR code',
        'body' => ['content', 'format' => 'png/svg', 'size']
    ],
    'POST /api/qrcode/booking' => [
        'description' => 'Generate booking QR code',
        'body' => ['booking_id', 'passenger_name', 'flight_info']
    ],

    // ============ PAYMENT APIs ============
    'POST /api/payment/stripe/create-intent' => [
        'description' => 'Create Stripe payment intent',
        'body' => ['amount', 'currency', 'description']
    ],
    'POST /api/payment/stripe/verify' => [
        'description' => 'Verify Stripe payment',
        'body' => ['payment_intent_id']
    ],
    'POST /api/payment/paypal/create-order' => [
        'description' => 'Create PayPal order',
        'body' => ['amount', 'currency', 'description']
    ],
    'POST /api/payment/paypal/capture' => [
        'description' => 'Capture PayPal payment',
        'body' => ['order_id']
    ],

    // ============ TRANSLATION APIs ============
    'POST /api/translate' => [
        'description' => 'Translate text',
        'body' => ['text', 'target' => 'fr/en/es/de', 'source' => 'auto']
    ],
    'POST /api/translate/detect' => [
        'description' => 'Detect language',
        'body' => ['text']
    ],
    'GET /api/translate/languages' => [
        'description' => 'Get supported languages'
    ],

    // ============ AI APIs ============
    'POST /api/ai/chat' => [
        'description' => 'Chat with AI',
        'body' => ['prompt']
    ],
    'POST /api/ai/circuit-suggestions' => [
        'description' => 'Get circuit suggestions',
        'body' => ['destination', 'preferences']
    ],
    'POST /api/ai/sentiment' => [
        'description' => 'Analyze sentiment',
        'body' => ['text']
    ],

    // ============ MODERATION APIs ============
    'POST /api/moderation/analyze' => [
        'description' => 'Analyze text for moderation',
        'body' => ['text']
    ],
    'POST /api/moderation/check' => [
        'description' => 'Check toxicity',
        'body' => ['text', 'threshold']
    ],

    // ============ GEOLOCATION APIs ============
    'GET /api/geolocation/ip' => [
        'description' => 'Get location from IP',
        'params' => ['ip' => 'Optional IP address']
    ],
    'GET /api/geolocation/geocode' => [
        'description' => 'Geocode address',
        'params' => ['address']
    ],
    'GET /api/geolocation/reverse' => [
        'description' => 'Reverse geocode',
        'params' => ['lat', 'lng']
    ],

    // ============ EXCHANGE RATE APIs ============
    'GET /api/exchange/rate' => [
        'description' => 'Get exchange rate',
        'params' => ['from' => 'EUR', 'to' => 'USD']
    ],
    'GET /api/exchange/convert' => [
        'description' => 'Convert currency',
        'params' => ['amount', 'from', 'to']
    ],
    'GET /api/exchange/currencies' => [
        'description' => 'Get available currencies'
    ],

    // ============ CIRCUIT APIs ============
    'GET /api/circuit/info' => [
        'description' => 'Get destination info with weather',
        'params' => ['destination']
    ],
    'POST /api/circuit/itinerary' => [
        'description' => 'Generate smart itinerary',
        'body' => ['destination', 'preferences']
    ],
    'GET /api/circuit/weather-recommendations' => [
        'description' => 'Get weather-based recommendations',
        'params' => ['destination', 'date']
    ],

    // ============ ACTIVITY APIs ============
    'GET /api/activities/nearby' => [
        'description' => 'Get nearby activities',
        'params' => ['lat', 'lng', 'category']
    ],
    'GET /api/activities/search' => [
        'description' => 'Search activities',
        'params' => ['query', 'location']
    ],
    'POST /api/activities/recommendations' => [
        'description' => 'Get personalized recommendations',
        'body' => ['location', 'interests']
    ],

    // ============ FORUM APIs ============
    'POST /api/forum/moderate' => [
        'description' => 'Moderate forum post',
        'body' => ['content']
    ],
    'POST /api/forum/auto-reply' => [
        'description' => 'Generate auto-reply',
        'body' => ['question', 'context']
    ],
    'POST /api/forum/sentiment' => [
        'description' => 'Analyze forum sentiment',
        'body' => ['text']
    ],
];