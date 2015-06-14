<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\AlumnoProvider;
use Lib\Providers\EstudioProvider;
use Lib\Providers\ConocimientoProvider;
use Lib\Providers\EmpresaProvider;
use Lib\Providers\CategoriaActividadProvider;

use Entities\Alumno;

/*** Listados ***/

$app->get('students/lists', function () use ($app) {
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();

    return $app['twig']->render('lists/students.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'knowledges' => $knowledges,
        'studies' => $studies
    ));
})->bind('student_lists');

$app->get('students/all/{page}', function ($page) use ($app) {
    $studentsPages = null;

    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();

    $AlumnoProvider = new AlumnoProvider($app);
    $students = $AlumnoProvider->getAlumnos();
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $AlumnoProvider->getAlumnos($page);
    }

    return $app['twig']->render('lists/students.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'students' => $students,
        'pages' => $studentsPages,
        'knowledges' => $knowledges,
        'studies' => $studies,
        'all_list' => true
    ));
})->bind('students_all_list')->value('page', 1);

$app->get('students/study/{id}/{page}', function ($id, $page) use ($app) {
    $studentsPages = null;

    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    $EstudioProvider = new EstudioProvider($app);
    $studies = $EstudioProvider->getEstudios();

    $study = $EstudioProvider->getEstudio($id);
    $students = $EstudioProvider->getAlumnos($study);
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $EstudioProvider->getAlumnos($study, $page);
    }

    if(count($students) == 0){
        return $app['twig']->render('lists/students.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'students' => $students,
            'pages' => $studentsPages,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'study_list' => true,
            'study_id' => $id,
            'study_empty_list' => true
        ));
    } else {
        return $app['twig']->render('lists/students.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'students' => $students,
            'pages' => $studentsPages,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'study_list' => true,
            'study_id' => $id
        ));
    }
})->bind('students_study_list')->value('page', 1);

$app->get('students/knowledge/{id}/{page}', function ($id, $page) use ($app) {
    $studentsPages = null;

    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();

    $knowledge = $ConocimientoProvider->getConocimiento($id);
    $students = $ConocimientoProvider->getAlumnos($knowledge);
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $ConocimientoProvider->getAlumnos($knowledge, $page);
    }

    if(count($students) == 0){
        return $app['twig']->render('lists/students.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'students' => $students,
            'pages' => $studentsPages,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'knowledge_list' => true,
            'knowledge_id' => $id,
            'knowledge_empty_list' => true
        ));
    }
    return $app['twig']->render('lists/students.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'students' => $students,
        'pages' => $studentsPages,
        'knowledges' => $knowledges,
        'studies' => $studies,
        'knowledge_list' => true,
        'knowledge_id' => $id
    ));
})->bind('students_knowledge_list')->value('page', 1);

/*** Búsquedas ***/

$app->post('students/study/search', function (Request $request) use ($app) {
    $id = $request->request->get('study');
    return $app->redirect($app['url_generator']->generate('students_study_list', array('id' => $id)));
})->bind('study_search');

$app->post('students/knowledge_search', function (Request $request) use ($app) {
    $id = $request->request->get('knowledge');
    return $app->redirect($app['url_generator']->generate('students_knowledge_list', array('id' => $id)));
})->bind('knowledge_search');

$app->post('category/search', function (Request $request) use ($app) {
    $studentId = $request->request->get('student');
    $categoryId = $request->request->get('category');
    $searchType = $request->request->get('searchType');

    switch($searchType){
        case 'studies':
            return $app->redirect($app['url_generator']->generate('add_categorized_studies', array(
                'id' => $studentId,
                'category_id' => $categoryId
            )));
            break;
        case 'knowledges':
            return $app->redirect($app['url_generator']->generate('add_categorized_knowledges', array(
                'id' => $studentId,
                'category_id' => $categoryId
            )));
            break;
        case 'companies':
            return $app->redirect($app['url_generator']->generate('add_categorized_companies', array(
                'id' => $studentId,
                'category_id' => $categoryId
            )));
    }
})->bind('category_search');

$app->post('students/custom/search', function (Request $request) use ($app) {
    $criteria = array();

    $category = $request->request->get('knowledge');
    $study = $request->request->getAlnum('study');
    $nif = $request->request->get('nif');
    $name = $request->request->get('nombre');
    $surnames = $request->request->get('apellidos');

    if(!empty($category)) $criteria['knowledge'] = $category;
    if(!empty($study)) $criteria['study'] = $study;
    if(!empty($nif)) $criteria['nif'] = $nif;
    if(!empty($name)) $criteria['name'] = $name;
    if(!empty($surnames)) $criteria['surnames'] = $surnames;

    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();

    $AlumnoProvider = new AlumnoProvider($app);
    $students = $AlumnoProvider->getAlumnosBy($criteria);

    return $app['twig']->render('lists/students.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'students' => $students,
        'knowledges' => $knowledges,
        'studies' => $studies,
        'custom_list' => true
    ));
})->bind('students_custom_list');

/*** Operaciones CRUD ***/

$app->put('student/create', function (Request $request) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);

    $nif = $request->request->getAlnum("nif");
    $name = $request->request->getAlpha("nombre");
    $surnames = $request->request->get("apellidos");
    if($surnames == '') $surnames = $request->request->get("small_apellidos");
    $address = $request->request->get("direccion");
    $cp = $request->request->get("cp");
    if($cp == '') $cp = $request->request->get("small_cp");
    $tlf = $request->request->get("telefono");
    $email = $request->request->get("email");

    $student = new Alumno($nif, $name, $surnames, $address, $cp, $tlf, $email);
    $AlumnoProvider->createAlumno($student);

    return $app['twig']->render('forms/student-form.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'form_type' => 'edit',
        'student' => $student,
        'new_student' => true
    ));
})->bind('student_new');

$app->get('student/{id}/edit', function ($id) use ($app) {
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

$app->post('student/update', function (Request $request) use ($app) {
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

$app->delete('student/{id}/remove', function ($id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $AlumnoProvider->removeAlumno($student);

    return new Response(200);
})->bind('student_remove');

/*** Asociación de entidades ***/

/*** Estudios *///Listados

$app->get('student/{id}/studies/{page}', function ($id, $page) use ($app) {
    $studiesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $EstudioProvider = new EstudioProvider($app);
    $studies = $EstudioProvider->getEstudios();
    foreach($studies as $i => $value){
        if($student->getEstudiosTitulos()->contains($value))
            unset($studies[$i]);
    }
    if(count($studies) > 5){
        $pagination = $app['pagination'](count($studies), $page);
        $studiesPages = $pagination->build();
        $studies = $EstudioProvider->getEstudios($page);
    }

    return $app['twig']->render('forms/user-adding.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'studies' => $studies,
        'categories' => $categories,
        'pages' => $studiesPages,
        'student_studies_list' => true
    ));
})->bind('add_studies')->value('page', 1);

$app->get('student/{id}/studies/category/{category_id}/{page}', function ($id, $category_id, $page) use ($app) {
    $studiesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $EstudioProvider = new EstudioProvider($app);
    $studies = $EstudioProvider->getEstudiosbyCategory($category_id);
    if(count($studies) > 5){
        $pagination = $app['pagination'](count($studies), $page);
        $studiesPages = $pagination->build();
        $studies = $EstudioProvider->getEstudiosbyCategory($category_id, $page);
    }

    return $app['twig']->render('forms/user-adding.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'studies' => $studies,
        'pages' => $studiesPages,
        'category_id' => $category_id,
        'student_categorized_studies_list' => true
    ));
})->bind('add_categorized_studies')->value('page', 1);

/*** Estudios *///Adición

$app->get('student/{id}/study/{study_id}/add', function ($id, $study_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EstudioProvider = new EstudioProvider($app);
    $study = $EstudioProvider->getEstudio($study_id);

    $student->addStudy($study);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('add_study');

/*** Estudios *///Eliminación

$app->delete('student/{id}/study/{study_id}/remove', function ($id, $study_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EstudioProvider = new EstudioProvider($app);
    $study = $EstudioProvider->getEstudio($study_id);

    $student->removeStudy($study);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('remove_study');

/*** Conocimientos *///Listados

$app->get('student/{id}/knowledges/{page}', function ($id, $page) use ($app) {
    $knowledgesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();
    foreach($knowledges as $i => $value){
        if($student->getConocimientosHabilidades()->contains($value))
            unset($knowledges[$i]);
    }
    if(count($knowledges) > 5){
        $pagination = $app['pagination'](count($knowledges), $page);
        $knowledgesPages = $pagination->build();
    }

    return $app['twig']->render('forms/user-adding.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'knowledges' => $knowledges,
        'pages' => $knowledgesPages,
        'categories' => $categories,
        'student_knowledges_list' => true
    ));
})->bind('add_knowledges')->value('page', 1);

/*** Conocimientos *///Adición

$app->get('student/{id}/knowledge/{knowledge_id}/add', function ($id, $knowledge_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledge = $ConocimientoProvider->getConocimiento($knowledge_id);

    $student->addKnowledge($knowledge);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('add_knowledge');

/*** Conocimientos *///Eliminación

$app->delete('student/{id}/knowledge/{knowledge_id}/remove', function ($id, $knowledge_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledge = $ConocimientoProvider->getConocimiento($knowledge_id);

    $student->removeKnowledge($knowledge);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('remove_knowledge');

/*** Empresas *///Listados

$app->get('student/{id}/companies/{page}', function ($id, $page) use ($app) {
    $companiesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $EmpresaProvider = new EmpresaProvider($app);
    $companies = $EmpresaProvider->getCompanies();
    foreach($companies as $i => $value){
        if($student->getEmpresas()->contains($value))
            unset($companies[$i]);
    }
    if(count($companies) > 5){
        $pagination = $app['pagination'](count($companies), $page);
        $companiesPages = $pagination->build();
    }

    return $app['twig']->render('forms/user-adding.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'student' => $student,
        'companies' => $companies,
        'pages' => $companiesPages,
        'categories' => $categories,
        'student_companies_list' => true
    ));
})->bind('add_companies')->value('page', 1);

/*** Empresas *///Adición

$app->get('student/{id}/company/{company_id}/add', function ($id, $company_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getCompany($company_id);

    $student->addCompany($company);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('add_company');

/*** Empresas *///Eliminación

$app->delete('student/{id}/company/{company_id}/remove', function ($id, $company_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getCompany($company_id);

    $student->removeCompany($company);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('remove_company');