<?php

//Configuración de Twig
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        return sprintf('../web/assets/%s', $asset);
    }));

    return $twig;
}));
$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');
