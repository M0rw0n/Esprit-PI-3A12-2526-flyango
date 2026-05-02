<?php

use App\Kernel;

// Increase execution time and memory for slow class loading on Windows
ini_set('max_execution_time', 120);
ini_set('memory_limit', '512M');

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
