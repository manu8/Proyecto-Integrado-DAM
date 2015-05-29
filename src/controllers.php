<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$GLOBALS['DOMAIN'] = '@ieslasfuentezuelas.com';

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'domain' => $GLOBALS['DOMAIN']
    ));
})
->bind('home');

//Users login routes
$app->get('/login', function(Request $request) use ($app) {
    $user = null;
    $token = $app['security']->getToken();
    if($token != null){
        $user = $token->getUser();
    }
    return $app['twig']->render('index.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'user' => $user,
        'error' => $app['security.last_error']($request),
        'last_email' => $app['session']->get('security.last_username'),
    ));
});

$app->get('/alumnos/lists', function (Request $request) use ($app) {
    return $app['twig']->render('filters-content.html.twig', array(
        'domain' => $GLOBALS['DOMAIN']
    ));
})
->bind('alumnos_lists');

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
