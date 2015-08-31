<?php

use Lib\Mailer;
use Symfony\Component\HttpFoundation\Request;

use Lib\Providers\UserProvider;

$app->get('user/login', function(Request $request) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    //if user is not enabled
    if (!is_null($user) && !$user->isEnabled()) {
        return $app['twig']->render('forms/login.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            'user_not_activated' => true,
            'email' => $user->getEmail()
        ));
    }

    return $app['twig']->render('forms/login.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

$app->post('/user/register', function(Request $request) use ($app) {
    $UserProvider = new UserProvider($app);

    $username = $request->request->get('username');

    $user = $UserProvider->createUser(
        $username . $GLOBALS['MAILING_DOMAIN'],
        $request->request->get('password')
    );

    $user->setUsername($username);

    if (!$UserProvider->validate($user)) {
        return $app['twig']->render('forms/user-forms.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'duplicate_user' => true,
            'last_username' => $app['session']->get('_security.last_username')
        ));
    }

    $user->setEnabled(false);
    $user->setConfirmationToken($user->generateToken());

    $UserProvider->persistUser($user);

    // Send email confirmation.
    $mailer = new Mailer($app['mailer'], $app['url_generator'], $app['twig']);
    $mailer->sendConfirmationMessage($user);

    return $app['twig']->render('forms/login.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'new_user' => true,
        'last_username' => $app['session']->get('_security.last_username')
    ));
})->bind('user-new');

$app->get('/user/edit', function() use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!$user) $form_type = "edit";
    else $form_type = 'new';

    return $app['twig']->render('forms/user-forms.html.twig', array(
        'form_type' => $form_type,
        'domain' => $GLOBALS['DOMAIN']
    ));
})->bind('user-edit');

$app->post('/user/{id}/update', function($id) use ($app) {
    $UserProvider = new UserProvider($app);

    $user = $UserProvider->getUser($id);
    $currentUser = $UserProvider->getCurrentUser(); //Before update user data

    if ($UserProvider->validate($user)) $UserProvider->updateUser($user);
    else {
        return $app['twig']->render('forms/user-forms.html.twig', array(
            'form_type' => 'edit',
            'domain' => $GLOBALS['DOMAIN']
        ));
    }

    //Resend user activation if email is modified
    if($user->getUsername() != $currentUser->getUsername()){
        $mailer = new Mailer($app['mailer'], $app['url_generator'], $app['twig']);
        $mailer->sendConfirmationMessage($user);

        return $app->redirect($app['url_generator']->generate('/logout')); //User logout until activation
    }

    return $app->redirect($app['url_generator']->generate('user-edit', array(
        'form_type' => 'edit',
        'domain' => $GLOBALS['DOMAIN'],
        'update_user' => true
    )));
})->bind('user-update');

$app->post('/user/{id}/delete', function($id) use ($app) {
    $UserProvider = new UserProvider($app);

    $user = $UserProvider->getUser($id);

    $UserProvider->deleteUser($user);

    return $app->redirect($app['url_generator']->generate('user_logout')); //User logout
})->bind('user-delete');

$app->get('/user/confirm-email/{token}', function($token) use ($app) {
    $user = $app['orm.em']->getRepository('Entities\Usuario')->findOneBy(array('confirmationToken' => $token));
    if (!$user) {
        return $app['twig']->render('forms/login.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'token_expired' => true,
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    $user->setConfirmationToken(null);
    $user->setEnabled(true);

    $UserProvider = new UserProvider($app);
    $UserProvider->updateUser($user);
    $UserProvider->loginUser($user); //LogIn user after confirmation

    return $app['twig']->render('forms/login.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'activate_user' => true,
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('confirm-email');


$app->post('user/resend_email', function(Request $request) use ($app) {
    $email = $request->request->get('email');

    $user = $app['orm.em']->getRepository('Entities\Usuario')->findOneBy(array('email' => $email));
    if (!$user) {
        throw new NotFoundHttpException('No user account was found with that email address.');
    }

    if (!$user->getConfirmationToken()) {
        $user->setConfirmationToken($user->generateToken());
        $UserProvider = new UserProvider($app);
        $UserProvider->updateUser($user);
    }

    $mailer = new Mailer($app['mailer'], $app['url_generator'], $app['twig']);
    $mailer->sendConfirmationMessage($user);

    return $app['twig']->render('forms/login.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'email_send' => true,
        'last_username' => $email
    ));
})->bind('resend-email');

$app->match('user/forgot_password', function(Request $request) use ($app) {
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $user = $app['orm.em']->getRepository('Entities\Usuario')->findOneBy(array('email' => $email));
        if ($user) {
            if (!$user->getConfirmationToken()) {
                $user->setConfirmationToken($user->generateToken());
            }

            $UserProvider = new UserProvider($app);
            $UserProvider->updateUser($user);

            $mailer = new Mailer($app['mailer'], $app['url_generator'], $app['twig']);
            $mailer->sendResetMessage($user);

            return $app['twig']->render('forms/login.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'reset_password' => true,
                'last_username' => $app['session']->get('_security.last_username'),
            ));
        }
    }

    return $app['twig']->render('forms/forgot-password.html.twig', array(
        'sender' => $GLOBALS['SENDER_EMAIL']
    ));
})->method('GET|POST')->bind('forgot-password');

$app->match('user/reset_password', function(Request $request) use ($app) {
    if ($request->isMethod('POST')) {
        $UserProvider = new UserProvider($app);
        $user = $UserProvider->getCurrentUser();
        if ($user) {
            $UserProvider->setUserPassword($user, $request->request->get('password'));
            $UserProvider->updateUser($user);

            return $app['twig']->render('forms/login.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'reset_password' => true,
                'last_username' => $app['session']->get('_security.last_username'),
            ));
        }
    }

    return $app['twig']->render('forms/reset-password.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN']
    ));
})->method('GET|POST')->bind('reset-password');