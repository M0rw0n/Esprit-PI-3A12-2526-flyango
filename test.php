<?php
require 'vendor/autoload.php';
require 'config/bootstrap.php';

use App\Service\GeocodingService;

$geo = new GeocodingService('');
$result = $geo->getCoordinatesWithKey('Tunis');
print_r($result);