<?php

use App\Kernel;

$autoloadFile = __DIR__ . '/vendor/autoload_runtime.php';
require $autoloadFile;
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
