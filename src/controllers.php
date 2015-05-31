<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Lib\Providers\UserProvider;

//Request::setTrustedProxies(array('127.0.0.1'));

$GLOBALS['DOMAIN'] = '@ieslasfuentezuelas.com';
$GLOBALS['MESSAGE'] = 'Pulsa sobre el enlace para activar su usuario.\n\n';

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'domain' => $GLOBALS['DOMAIN']
    ));
})
->bind('home');

//Users routes
$app->get('/user/login', function(Request $request) use ($app) {
    $user = null;
    $token = $app['security']->getToken();
    if($token != null){
        $user = $token->getUser();
    }
    return $app['twig']->render('login-form.html.twig', array(
        'form_type' => 'login',
        'domain' => $GLOBALS['DOMAIN'],
        'user' => $user,
        'error' => $app['security.last_error']($request),
        'last_email' => $app['session']->get('_security.last_username'),
    ));
});

$app->get('/user/{id}/activate', function($id) use ($app) {
    $UserProvider = new UserProvider($app);
    $UserProvider->activateUser($id);

    return $app['twig']->render('forms.html.twig', array(
        'form_type' => 'login',
        'activate_user' => true
    ));
})
->bind('activate_user');

$app->put('/user/new', function(Request $request) use ($app) {
    $UserProvider = new UserProvider($app);
    $email = $request->request->get('email') . $GLOBALS['DOMAIN'];
    $password = $request->request->get('password');
    $UserProvider->createUser(new Entities\Usuario($email, $password));

    $message = \Swift_Message::newInstance()
        ->setSubject('User Validation')
        ->setFrom(array('noreply' . $GLOBALS['DOMAIN']))
        ->setTo(array($email))
        ->setBody($GLOBALS['MESSAGE'] .
            $app['url_generator']->generate('activate_user', array(
                'id' => $UserProvider->loadUserByUsername($email)->getId()
            ))
        );
    $app['mailer']->send($message);

    return $app['twig']->render('forms.html.twig', array(
        'new_user' => true
    ));
})
->bind('new_user');

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
