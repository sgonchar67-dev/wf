<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context) {
    $APP_DEBUG = $_COOKIE['APP_DEBUG'] ?? $context['APP_DEBUG'] ?? false;
    return new Kernel($context['APP_ENV'], (bool) $APP_DEBUG);
};
