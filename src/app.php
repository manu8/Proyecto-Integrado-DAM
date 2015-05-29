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
    "orm.em.options" => array(
        "mappings" => array(
            array(
                "type" => "annotation",
                "namespace" => "Entities",
                "path" => __DIR__."/entities",
                "use_simple_annotation_reader" => false,
            ),
        ),
    ),
));

//Implementación de seguridad
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'dev' => array(
            'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
            'security' => false
        ),
        'secured' => array(
            'pattern' => '^/admin/',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/admin/login_check'
            ),
            'users' => $app->share(function() use ($app) {
                return new Lib\Providers\UserProvider($app);
            }),
            'logout' => array('logout_path' => '/admin/logout'),
        )
    ),
    'security.encoders' => array('Entities\Usuario' => array(
        'algorithm' => 'sha1',
        'iterations' => 4,
        'encode_as_base64' => false
    ))
));

return $app;
