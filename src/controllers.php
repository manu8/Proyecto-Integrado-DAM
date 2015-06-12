<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Lib\Providers\UserProvider;
use Lib\Providers\AlumnoProvider;
use Lib\Providers\EstudioProvider;
use Lib\Providers\ConocimientoProvider;
use Lib\Providers\EmpresaProvider;

use Entities\Usuario;
use Entities\Alumno;

//Request::setTrustedProxies(array('127.0.0.1'));

/*** Ruta principal ***/

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'domain' => $GLOBALS['DOMAIN']
    ));
})->bind('home');

/*** Rutas de usuarios ***/

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

/*** Rutas de alumnos ***/

$app->get('/student/lists', function () use ($app) {
    return $app['twig']->render('filters-content.html.twig', array(
        'domain' => $GLOBALS['DOMAIN']
    ));
})->bind('student_lists');

$app->post('/student/create', function (Request $request) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);

    $nif = $request->request->getAlnum("nif");
    $name = $request->request->getAlpha("nombre");
    $surnames = $request->request->get("apellidos");
    if($surnames == '') $surnames = $request->request->get("small_apellidos");
    $address = $request->request->get("direccion");
    $cp = $request->request->get("cp");
    if($cp == '') $cp = $request->request->get("small_cp");
    $tlf = $request->request->get("telefono");
    $email = $request->request->getAlnum("email");

    $student = new Alumno($nif, $name, $surnames, $address, $cp, $tlf, $email);
    $AlumnoProvider->createAlumno($student);

    return $app['twig']->render('forms/student-form.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'form_type' => 'edit',
        'student' => $student,
        'new_student' => true
    ));
})->bind('student_new');

$app->get('/student/{id}/edit', function ($id, $pagination = null, $page = null) use ($app) {
    $student = null;
    if($id != 0){
        $form_type = 'edit';
        $AlumnoProvider = new AlumnoProvider($app);
        $student = $AlumnoProvider->getAlumno($id);
    } else $form_type = 'new';

    return $app['twig']->render('forms/student-form.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'form_type' => $form_type,
        'student' => $student
    ));
})->bind('student_edit');

$app->post('/student/update', function (Request $request) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $request->request->get("student");
    $AlumnoProvider->updateAlumno($student);

    return $app['twig']->render('forms/student-form.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'form_type' => 'edit',
        'student' => $student,
        'update_student' => true
    ));
})->bind('student_update');

$app->post('/student/remove', function (Request $request) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $request->request->get("student");
    $AlumnoProvider->removeAlumno($student);

    return $app->redirect($app['url_generator']->generate('student_lists', array(
        'domain' => $GLOBALS['DOMAIN'],
        'delete_student' => true
    )));
})->bind('student_remove');

$app->get('/student/{id}/studies/{page}', function ($id, $page) use ($app) {
    $studiesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $EstudioProvider = new EstudioProvider($app);
    $studies = $EstudioProvider->getEstudios();
    if(count($studies) > 5){
        $pagination = $app['pagination'](count($studies), $page);
        $studiesPages = $pagination->build();
    }

    return $app['twig']->render('list/studies.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'studies' => $studies,
        'pages' => $studiesPages,
        'current_page' => $page,
        'add_studies' => true
    ));
})->bind('add_studies')->value('page', 1);

$app->get('/student/{id}/knowledges/{page}', function ($id, $page) use ($app) {
    $knowledgesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    if(count($knowledges) > 5){
        $pagination = $app['pagination'](count($knowledges), $page);
        $knowledgesPages = $pagination->build();
    }

    return $app['twig']->render('lists/knowledges.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'knowledges' => $knowledges,
        'pages' => $knowledgesPages,
        'current_page' => $page,
        'add_knowledges' => true
    ));
})->bind('add_knowledges')->value('page', 1);

$app->get('/student/{id}/companies/{page}', function ($id, $page) use ($app) {
    $companiesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $EmpresaProvider = new EmpresaProvider($app);
    $companies = $EmpresaProvider->getCompanies();
    if(count($companies) > 5){
        $pagination = $app['pagination'](count($companies), 1);
        $companiesPages = $pagination->build();
    }

    return $app['twig']->render('lists/knowledges.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'companies' => $companies,
        'pages' => $companiesPages,
        'current_page' => $page,
        'add_companies' => true
    ));
})->bind('add_companies')->value('page', 1);

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
