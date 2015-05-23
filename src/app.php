<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;


/**** INICIALIZACIÓN Y CONFIGURACIÓN DE LA APLICACIÓN ****/

$app = new Application();

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());

//Integración de vistas con Twig
$app->register(new TwigServiceProvider());
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('../web/assets/%s', $asset);
    }));

    return $twig;
}));

//Integración de la BD
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'dbname' => 'curriculumsdb',
        'password' => '',
        'charset' => 'UTF-8'
    ),
));
$app->register(new DoctrineOrmServiceProvider, array(
    "orm.em.options" => array(
        "mappings" => array(
            array(
                "type" => "annotation",
                "namespace" => "Entities",
                "path" => __DIR__."/src/Entities",
            ),
        ),
    ),
));

return $app;
