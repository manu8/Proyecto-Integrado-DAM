<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));
Request::enableHttpMethodParameterOverride(); //Habilitación de métodos alternativos de HTTP (PUT, DELETE)

/*** Ruta principal ***/

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'domain' => $GLOBALS['DOMAIN']
    ));
})->bind('home');

require __DIR__.'/controllers/UsersController.php'; //Controlador de rutas de usuario
require __DIR__.'/controllers/StudentsController.php'; //Controlador de rutas de alumnos

/*** Páginas de error ***/

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
