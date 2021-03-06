<?php

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
$app['security.firewalls'] = array(
    'dev_and_assets' => array(
        'pattern' => '^/(_(profiler|wdt)|assets)/',
        'security' => false
    ),
    'secured' => array(
        'pattern' => '^/.*$',
        'remember_me' => array(
            'key' => 'VhrvJ4qx6F',
            'always_remember_me' => true,
            'lifetime' => 604800 # 1 week
        ),
        'form' => array(
            'login_path' => '/user/login',
            'check_path' => '/user/login_check',
        ),
        'users' => $app->share(function ($app) {
            return $app['user.manager'];
        }),
        'logout' => array('logout_path' => '/user/logout'),
        'anonymous' => true
    )
);
/*** Session ***/
$app['session.storage.options'] = array(
    'cookie_lifetime' => 604800 # 1 week
);

//Configuración de servidor de correo
$app['swiftmailer.options'] = array(
    'username' => 'postmaster'
);

//Configuración de administrador de usuarios
$app['user.options'] = array(
    'emailConfirmation' => array(
        'required' => true
    )
);