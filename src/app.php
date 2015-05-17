<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;


/**** INICIALIZACIÓN Y CONFIGURACIÓN DE LA APLICACIÓN ****/

$app = new Application();
$app['debug'] = true; //Activa la información de depuración


$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());

//Integración de vistas con Twig
$app->register(new TwigServiceProvider(), array(
	'templates_path' => __DIR__.'../templates'
));
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('../web/assets/%s', $asset);
    }));

    return $twig;
}));

return $app;
