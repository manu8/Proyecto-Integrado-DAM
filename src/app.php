<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;


/**** INICIALIZACIÓN Y CONFIGURACIÓN DE LA APLICACIÓN ****/

$app = new Application();

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());

//Integración de vistas con Twig
$app->register(new TwigServiceProvider());

//Integración de la BD
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'dbname' => 'curriculumsdb',
        'password' => '',
        'charset' => 'utf8'
    ),
));
$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => __DIR__.'/../vendor/doctrine/orm/lib/Doctrine/ORM/Proxy',
    'orm.proxies_namespace' => 'DoctrineProxy',
    'orm.auto_generate_proxies' => true,
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'Entities',
                'path' => __DIR__.'/entities'
            ),
        ),
    ),
));

//Implementación de seguridad
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new SecurityServiceProvider());

//Implementación del servidor de correo para verificación de usuario
$app->register(new SwiftmailerServiceProvider());

return $app;
