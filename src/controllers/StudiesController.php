<?php

use Entities\EstudioTitulo;
use Lib\Providers\ConocimientoProvider;
use Lib\Providers\EmpresaProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\EstudioProvider;
use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\AlumnoProvider;

/*** Listados ***/

$app->get('studies/list/{page}', function ($page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studiesPages = null;

        $CategoriaProvider = new CategoriaActividadProvider($app);
        $categories = $CategoriaProvider->getCategorias();

        $EstudioProvider = new EstudioProvider($app);
        $studies = $EstudioProvider->getEstudios();
        if(count($studies) > 5){
            $pagination = $app['pagination'](count($studies), $page);
            $studiesPages = $pagination->build();
            $studies = $EstudioProvider->getEstudios($page);
        }

        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'studies' => $studies,
            'pages' => $studiesPages,
            'categories' => $categories,
            'studies_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('studies-list')->value('page', 1);

$app->post('studies/category/{page}', function (Request $request, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studiesPages = null;

        $CategoriaProvider = new CategoriaActividadProvider($app);
        $categories = $CategoriaProvider->getCategorias();

        $id = $request->request->get('category');
        $category = $CategoriaProvider->getCategoria($id);
        $studies = $CategoriaProvider->getStudies($category);
        if(count($studies) > 5){
            $pagination = $app['pagination'](count($studies), $page);
            $studiesPages = $pagination->build();
            $studies = $CategoriaProvider->getCompanies($category, $page);
        }

        if(count($studies) == 0){
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'studies' => $studies,
                'pages' => $studiesPages,
                'categories' => $categories,
                'studies_list' => true,
                'category_id' => $id,
                'category_empty_list' => true
            ));
        } else {
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'studies' => $studies,
                'pages' => $studiesPages,
                'categories' => $categories,
                'studies_list' => true,
                'category_id' => $id
            ));
        }
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('studies-category-list')->value('page', 1);

/*** Operaciones CRUD ***/

$app->put('study/create', function (Request $request) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);

        $name = $request->request->get("name");
        $description = $request->request->get("description");

        $study = new EstudioTitulo($name, $description);

        $EstudioProvider->createEstudio($study);

        return $app['twig']->render('forms/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'form_type' => 'edit',
            'study' => $study,
            'new_study' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('study-new');

$app->get('study/{id}/edit', function ($id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $study = null;
        $categories = null;
        if($id != 0){
            $form_type = 'edit';
            $EstudioProvider = new EstudioProvider($app);
            $study = $EstudioProvider->getEstudio($id);
        } else $form_type = 'new';

        return $app['twig']->render('forms/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'form_type' => $form_type,
            'study' => $study
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-edit');

$app->post('study/{id}/update', function (Request $request, $id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $study->setDenominacion($request->request->get('nombre'));
        $study->setContacto($request->request->get('descripcion'));
        $EstudioProvider->updateEstudio($study);

        return $app['twig']->render('forms/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'form_type' => 'edit',
            'study' => $study,
            'update_study' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-update');

$app->delete('study/{id}/remove', function ($id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $EstudioProvider->removeEstudio($study);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-remove');

/*** Asociación de entidades ***/

/*** Categorías *///Listados

$app->get('study/{id}/categories/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $categoriesPages = null;

        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);

        $CategoriaProvider = new CategoriaActividadProvider($app);
        $categories = $CategoriaProvider->getCategorias();
        foreach($categories as $i => $value){
            if($study->getCategorias()->contains($value))
                unset($categories[$i]);
        }

        if(count($categories) > 5){
            $pagination = $app['pagination'](count($categories), $page);
            $categoriesPages = $pagination->build();
            $categories = $CategoriaProvider->getCategorias($page);
        }

        return $app['twig']->render('additions/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'study' => $study,
            'categories' => $categories,
            'pages' => $categoriesPages,
            'study_categories_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-add-categories')->value('page', 1);

/*** Categorías *///Adición

$app->get('study/{id}/category/{category_id}/add', function ($id, $category_id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $CategoryProvider = new CategoriaActividadProvider($app);
        $category = $CategoryProvider->getCategoria($category_id);

        $study->addCategoria($category);
        $app['orm.em']->flush();

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-add-category');

/*** Categorías *///Eliminación

$app->delete('study/{id}/category/{category_id}/remove', function ($id, $category_id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($category_id);

        $study->removeCategoria($category);
        $em = $app['orm.em'];
        $em->flush();

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-remove-category');

/*** Alumnos *///Listados

$app->get('study/{id}/students/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $studies = $EstudioProvider->getEstudios();

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();
        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnos();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();
        foreach($students as $i => $value){
            if($study->getAlumnos()->contains($value))
                unset($students[$i]);
        }
        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnos($page);
        }

        return $app['twig']->render('additions/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'study' => $study,
            'students' => $students,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'companies' => $companies,
            'pages' => $studentsPages,
            'study_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-add-students')->value('page', 1);

$app->get('study/{id}/students/study/{study_id}/{page}', function ($id, $study_id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $study = $EstudiosProvider->getEstudio($id);

        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy(array('study' => $study_id));
        foreach($students as $i => $value){
            if($study->getAlumnos()->contains($value))
                unset($students[$i]);
        }

        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnosBy(array('study' => $study_id), $page);
        }

        if(count($students) == 0) {
            return $app['twig']->render('additions/study.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'study' => $study,
                'students' => $students,
                'pages' => $studentsPages,
                'study_id' => $study_id,
                'studies' => $studies,
                'companies' => $companies,
                'knowledges' => $knowledges,
                'study_students_list' => true,
                'study_empty_list' => true
            ));
        }

        return $app['twig']->render('additions/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'study' => $study,
            'students' => $students,
            'pages' => $studentsPages,
            'study_id' => $study_id,
            'studies' => $studies,
            'companies' => $companies,
            'knowledges' => $knowledges,
            'study_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('add-study-students-study')->value('page', 1);

$app->get('study/{id}/students/knowledge/{knowledge_id}/{page}', function ($id, $knowledge_id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $study = $EstudiosProvider->getEstudio($id);

        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy(array('knowledge' => $knowledge_id));
        foreach($students as $i => $value){
            if($study->getAlumnos()->contains($value))
                unset($students[$i]);
        }

        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnosBy(array('knowledge' => $knowledge_id), $page);
        }

        if(count($students) == 0) {
            return $app['twig']->render('additions/study.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'study' => $study,
                'students' => $students,
                'pages' => $studentsPages,
                'knowledge_id' => $knowledge_id,
                'studies' => $studies,
                'companies' => $companies,
                'knowledges' => $knowledges,
                'study_students_list' => true,
                'knowledge_empty_list' => true
            ));
        }

        return $app['twig']->render('additions/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'study' => $study,
            'students' => $students,
            'pages' => $studentsPages,
            'knowledge_id' => $knowledge_id,
            'studies' => $studies,
            'companies' => $companies,
            'knowledges' => $knowledges,
            'study_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('add-study-students-knowledge')->value('page', 1);

$app->get('study/{id}/students/company/{company_id}/{page}', function ($id, $company_id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $study = $EstudiosProvider->getEstudio($id);

        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy(array('company' => $company_id));
        foreach($students as $i => $value){
            if($study->getAlumnos()->contains($value))
                unset($students[$i]);
        }

        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnosBy(array('company' => $company_id), $page);
        }

        if(count($students) == 0) {
            return $app['twig']->render('additions/study.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'study' => $study,
                'students' => $students,
                'pages' => $studentsPages,
                'company_id' => $company_id,
                'studies' => $studies,
                'companies' => $companies,
                'knowledges' => $knowledges,
                'study_students_list' => true,
                'company_empty_list' => true
            ));
        }

        return $app['twig']->render('additions/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'study' => $study,
            'students' => $students,
            'pages' => $studentsPages,
            'company_id' => $company_id,
            'studies' => $studies,
            'companies' => $companies,
            'knowledges' => $knowledges,
            'study_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('add-study-students-company')->value('page', 1);

/*** Alumnos *///Adición

$app->get('study/{id}/student/{student_id}/add', function ($id, $student_id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $AlumnoProvider = new AlumnoProvider($app);
        $student = $AlumnoProvider->getAlumno($student_id);

        $student->addStudy($study);
        $em = $app['orm.em'];
        $em->flush();

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-add-student');

/*** Alumnos *///Eliminación

$app->delete('study/{id}/student/{student_id}/remove', function ($id, $student_id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $EstudioProvider = new EstudioProvider($app);
        $study = $EstudioProvider->getEstudio($id);
        $AlumnoProvider = new AlumnoProvider($app);
        $student = $AlumnoProvider->getAlumno($student_id);

        $student->removeStudy($study);
        $em = $app['orm.em'];
        $em->flush();

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('study-remove-student');