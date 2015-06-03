<?php

use Lib\Providers\UserProvider;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

//Configuración de Twig
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('../assets/%s', $asset);
    }));

    return $twig;
}));
$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

//Configuración de seguridad
$app['security.firewalls'] = array(
    'dev' => array(
        'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
        'security' => false
    ),
    'secured' => array(
        'pattern' => '^/admin/',
        'form' => array(
            'login_path' => '/user/login',
            'check_path' => '/admin/login_check'
        ),
        'users' => $app->share(function() use ($app) {
            return new UserProvider($app);
        }),
        'logout' => array('logout_path' => '/admin/logout')
    )
);
$app['security_encoder.digest'] = $app->share(function () {
    return new MessageDigestPasswordEncoder('sha1', false, 4);
});
