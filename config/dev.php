<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Silex\Provider\HttpFragmentServiceProvider;
use MJanssen\Provider\RoutingServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

// include the production configuration
require __DIR__.'/prod.php';

// enable the debug mode
$app['debug'] = true;
ErrorHandler::register();
ExceptionHandler::register();

$app->register(new HttpFragmentServiceProvider());
$app->register(new RoutingServiceProvider());

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/silex_dev.log',
));

$app->register(new WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../var/cache/profiler',
));
