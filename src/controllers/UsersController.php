<?php

use Lib\Providers\UserProvider;
use Entities\Usuario;

$app->get('/login', function(Request $request) use ($app) {
    $userProvider = new UserProvider($app);
    if($user = $userProvider->getCurrentUser()){
        return;
    }

    return $app['twig']->render('forms/login-form.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'user' => $user,
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->post('/user/new', function(Request $request) use ($app) {
    $userProvider = new UserProvider($app);
    $username = $request->request->getAlnum('username');
    $password = $request->request->getAlnum('password');
    $userProvider->createUser(new Usuario($username, $password));

    $message = \Swift_Message::newInstance()
        ->setSubject('User Validation')
        ->setFrom(array('noreply' . $GLOBALS['DOMAIN']))
        ->setTo(array($username . $GLOBALS['DOMAIN']))
        ->setBody($GLOBALS['ACTIVATION_MESSAGE'] .
            $app['url_generator']->generate('activate_user', array(
                'id' => $userProvider->loadUserByUsername($username)->getId()
            ))
        );
    $app['mailer']->send($message);

    return $app['twig']->render('forms/user-forms.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'new_user' => true
    ));
})->bind('user_create');

$app->get('/user/edit', function(Request $request) use ($app) {
    $token = $app['security']->getToken();
    if($token != null) $user = $token->getUser();

    if(!$user) $form_type = "edit";
    else $form_type = 'new';

    return $app['twig']->render('user-forms.html.twig', array(
        'form_type' => $form_type,
        'user' => $user,
        'domain' => $GLOBALS['DOMAIN'],
        'last_view' => $request->getRequestUri()
    ));
})->bind('user_edit');

$app->post('/user/update', function(Usuario $user) use ($app) {
    $currentUser = null;
    $token = $app['security']->getToken();
    if($token != null){
        $currentUser = $token->getUser();
    }

    $UserProvider = new UserProvider($app);
    $UserProvider->updateUser($user);
    if($user->getUsername() != $currentUser->getUsername()){
        $message = \Swift_Message::newInstance()
            ->setSubject('User Validation')
            ->setFrom(array('noreply' . $GLOBALS['DOMAIN']))
            ->setTo(array($user->getUsername() . $GLOBALS['DOMAIN']))
            ->setBody($GLOBALS['ACTIVATION_MESSAGE'] .
                $app['url_generator']->generate('activate_user', array(
                    'id' => $user->getId()
                ))
            );
        $app['mailer']->send($message);

        return $app->redirect($app['url_generator']->generate('/logout'));
    }

    return $app->redirect($app['url_generator']->generate('edit_user', array(
        'form_type' => 'edit',
        'user' => $user,
        'domain' => $GLOBALS['DOMAIN'],
        'update_user' => true
    )));
})->bind('user_update');

$app->get('/user/{id}/activate', function($id) use ($app) {
    $UserProvider = new UserProvider($app);
    $UserProvider->activateUser($id);

    return $app['twig']->render('forms/login-form.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'activate_user' => true
    ));
})->bind('user_activate');