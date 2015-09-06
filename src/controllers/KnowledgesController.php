<?php

use Entities\HabilidadConocimiento;
use Lib\Providers\EmpresaProvider;
use Lib\Providers\EstudioProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\ConocimientoProvider;
use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\AlumnoProvider;
use Lib\Providers\UserProvider;

/*** Listados ***/

$app->get('knowledges/list/{page}', function ($page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $knowledgesPages = null;

        $CategoriaProvider = new CategoriaActividadProvider($app);
        $categories = $CategoriaProvider->getCategorias();

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowleges = $ConocimientoProvider->getConocimientos();
        if(count($knowleges) > 5){
            $pagination = $app['pagination'](count($knowleges), $page);
            $knowlegesPages = $pagination->build();
            $knowleges = $ConocimientoProvider->getConocimientos($page);
        }

        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'knowledges' => $knowleges,
            'pages' => $knowledgesPages,
            'categories' => $categories,
            'knowledges_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('knowledges-list')->value('page', 1);

$app->get('knowledeges/category/{page}', function (Request $request, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')) {
        $knowledgesPages = null;

        $CategoriaProvider = new CategoriaActividadProvider($app);
        $categories = $CategoriaProvider->getCategorias();

        $id = $request->request->get('category');
        $category = $CategoriaProvider->getCategoria($id);

        $knowledges = $CategoriaProvider->getKnowledges($category);
        if (count($knowledges) > 5) {
            $pagination = $app['pagination'](count($knowledges), $page);
            $knowledgesPages = $pagination->build();
            $knowledges = $CategoriaProvider->getKnowledges($category, $page);
        }

        if (count($knowledges) == 0) {
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'knowledges' => $knowledges,
                'pages' => $knowledgesPages,
                'categories' => $categories,
                'category_list' => true,
                'category_id' => $id,
                'category_empty_list' => true
            ));
        } else {
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'knowledges' => $knowledges,
                'pages' => $knowledgesPages,
                'categories' => $categories,
                'category_list' => true,
                'category_id' => $id
            ));
        }
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledges-category-list')->value('page', 1);

/*** Operaciones CRUD ***/

$app->put('knowledge/create', function (Request $request) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $ConocimientoProvider = new ConocimientoProvider($app);

        $name = $request->request->get("name");
        $description = $request->request->get("description");

        $knowledge = new HabilidadConocimiento($name, $description);
        $ConocimientoProvider->createConocimiento($knowledge);

        return $app['twig']->render('forms/knowledge.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'form_type' => 'edit',
            'knowledge' => $knowledge,
            'new_knowledge' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('knowledge-new');

$app->get('knowledge/{id}/edit', function ($id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $knowledge = null;
        $categories = null;
        if($id != 0){
            $form_type = 'edit';
            $ConocimientoProvider = new ConocimientoProvider($app);
            $knowledge = $ConocimientoProvider->getConocimiento($id);
        } else $form_type = 'new';

        return $app['twig']->render('forms/knowledge.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'form_type' => $form_type,
            'knowledge' => $knowledge
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge-edit');

$app->post('knowledge/{id}/update', function (Request $request, $id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $knowledge->setDenominacion($request->request->get('nombre'));
        $knowledge->setDescripcion($request->request->get('descripcion'));
        $ConocimientoProvider->updateConocimiento($knowledge);

        return $app['twig']->render('forms/company.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'form_type' => 'edit',
            'knowledge' => $knowledge,
            'update_knowledge' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge-update');

$app->delete('knowledge/{id}/remove', function ($id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $ConocimientoProvider->removeConocimiento($knowledge);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge-remove');

/*** Asociación de entidades ***/

/*** Categorías *///Listados

$app->get('knowledge/{id}/categories/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $categoriesPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);

        $CategoriaProvider = new CategoriaActividadProvider($app);
        $categories = $CategoriaProvider->getCategorias();
        foreach($categories as $i => $value){
            if($knowledge->getCategorias()->contains($value))
                unset($categories[$i]);
        }
        if(count($categories) > 5){
            $pagination = $app['pagination'](count($categories), $page);
            $categoriesPages = $pagination->build();
            $categories = $CategoriaProvider->getCategorias($page);
        }

        return $app['twig']->render('additions/knowledge.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'knowledge' => $knowledge,
            'categories' => $categories,
            'pages' => $categoriesPages,
            'knowledge_categories_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge-add-categories')->value('page', 1);

/*** Categorías *///Adición

$app->get('knowledge/{id}/category/{category_id}/add', function ($id, $category_id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($category_id);

        $knowledge->addCategory($category);
        $em = $app['orm.em'];
        $em->flush();

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge-add-category');

/*** Categorías *///Eliminación

$app->delete('knowledge/{id}/category/{category_id}/remove', function ($id, $category_id) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($category_id);

        $knowledge->removeCategory($category);
        $em = $app['orm.em'];
        $em->flush();

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge-remove-category');

/*** Alumnos *///Listados

$app->get('knowledge/{id}/students/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $EstudioProvider = new EstudioProvider($app);
        $studies = $EstudioProvider->getEstudios();
        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnos();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();
        foreach($students as $i => $value){
            if($knowledge->getAlumnos()->contains($value))
                unset($students[$i]);
        }
        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnos($page);
        }

        return $app['twig']->render('additions/knowledge.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'knowledge' => $knowledge,
            'students' => $students,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'companies' => $companies,
            'pages' => $studentsPages,
            'knowledge_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('add-knowledge-students')->value('page', 1);

$app->get('knowledge/{id}/students/study/{study_id}/{page}', function ($id, $study_id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $EstudioProvider = new EstudioProvider($app);
        $studies = $EstudioProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy(array('study' => $study_id));
        foreach($students as $i => $value){
            if($knowledge->getAlumnos()->contains($value))
                unset($students[$i]);
        }

        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnosBy(array('study' => $study_id), $page);
        }

        if(count($students) == 0) {
            return $app['twig']->render('additions/knowledge.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'knowledge' => $knowledge,
                'students' => $students,
                'pages' => $studentsPages,
                'study_id' => $study_id,
                'studies' => $studies,
                'companies' => $companies,
                'knowledges' => $knowledges,
                'knowledge_students_list' => true,
                'study_empty_list' => true
            ));
        }

        return $app['twig']->render('additions/study.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'knowledge' => $knowledge,
            'students' => $students,
            'pages' => $studentsPages,
            'study_id' => $study_id,
            'studies' => $studies,
            'companies' => $companies,
            'knowledges' => $knowledges,
            'knowledge_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('add-knowledge-students-study')->value('page', 1);

$app->get('knowledge/{id}/students/knowledge/{knowledge_id}/{page}', function ($id, $knowledge_id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $EstudioProvider = new EstudioProvider($app);
        $studies = $EstudioProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy(array('knowledge' => $knowledge_id));
        foreach($students as $i => $value){
            if($knowledge->getAlumnos()->contains($value))
                unset($students[$i]);
        }

        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnosBy(array('knowledge' => $knowledge_id), $page);
        }

        if(count($students) == 0) {
            return $app['twig']->render('additions/knowledge.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'knowledge' => $knowledge,
                'students' => $students,
                'pages' => $studentsPages,
                'knowledge_id' => $knowledge_id,
                'studies' => $studies,
                'companies' => $companies,
                'knowledges' => $knowledges,
                'knowledge_students_list' => true,
                'study_empty_list' => true
            ));
        }

        return $app['twig']->render('additions/knowledge.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'knowledge' => $knowledge,
            'students' => $students,
            'pages' => $studentsPages,
            'knowledge_id' => $knowledge_id,
            'studies' => $studies,
            'companies' => $companies,
            'knowledges' => $knowledges,
            'knowledge_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));
})->bind('add-knowledge-students-knowledge')->value('page', 1);

$app->get('knowledge/{id}/students/company/{company_id}/{page}', function ($id, $company_id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $knowledges = $ConocimientoProvider->getConocimientos();

        $EstudioProvider = new EstudioProvider($app);
        $studies = $EstudioProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy(array('company' => $company_id));
        foreach($students as $i => $value){
            if($knowledge->getAlumnos()->contains($value))
                unset($students[$i]);
        }

        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnosBy(array('company' => $company_id), $page);
        }

        if(count($students) == 0) {
            return $app['twig']->render('additions/knowledge.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'knowledge' => $knowledge,
                'students' => $students,
                'pages' => $studentsPages,
                'company_id' => $company_id,
                'studies' => $studies,
                'companies' => $companies,
                'knowledges' => $knowledges,
                'knowledge_students_list' => true,
                'company_empty_list' => true
            ));
        }

        return $app['twig']->render('additions/knowledge.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'knowledge' => $knowledge,
            'students' => $students,
            'pages' => $studentsPages,
            'company_id' => $company_id,
            'studies' => $studies,
            'companies' => $companies,
            'knowledges' => $knowledges,
            'knowledge_students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('add-knowledge-students-company')->value('page', 1);