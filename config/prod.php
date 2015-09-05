<?php

use Lib\Providers\UserProvider;

//Constantes de la aplicación
$GLOBALS['MAILING_DOMAIN'] = '@iesvirgendelcarmen.com';
$GLOBALS['SENDER_EMAIL'] = 'no-reply@iesvirgendelcarmen.com';

//Configuración de Twig
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('/assets/%s', $asset);
    }));

    return $twig;
}));
$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

//Configuración de seguridad
/*** Firewalls ***/
$app['security.firewalls'] = array(
    'secured' => array(
        'pattern' => '^.*$',
        'remember_me' => array(
            'key' => 'VhrvJ4qx6F',
            'always_remember_me' => true,
            'lifetime' => 604800 # 1 week
        ),
        'form' => array(
            'login_path' => '/user/login',
            'check_path' => '/user/login_check',
        ),
        'users' => $app->share(function() use ($app) {
            return new UserProvider($app);
        }),
        'logout' => array('logout_path' => '/user/logout'),
        'anonymous' => true
    )
);
