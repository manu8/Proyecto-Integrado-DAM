<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\EmpresaProvider;
use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\AlumnoProvider;
use Lib\Providers\UserProvider;
use Lib\Providers\EstudioProvider;
use Lib\Providers\ConocimientoProvider;

use Entities\Empresa;

/*** Listados ***/

$app->get('companies/list/{page}', function ($page) use ($app) {
    $companiesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $EmpresaProvider = new EmpresaProvider($app);
    $companies = $EmpresaProvider->getEmpresas();
    if(count($companies) > 5){
        $pagination = $app['pagination'](count($companies), $page);
        $companiesPages = $pagination->build();
        $companies = $EmpresaProvider->getEmpresas($page);
    }

    return $app['twig']->render('list-wrapper.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'companies' => $companies,
        'pages' => $companiesPages,
        'categories' => $categories,
        'companies_list' => true
    ));
})->bind('companies-list')->value('page', 1);

$app->get('companies/category/{id}/{page}', function ($id, $page) use ($app) {
    $companiesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $category = $CategoriaProvider->getCategoria($id);
    $companies = $CategoriaProvider->getCompanies($category);
    if(count($companies) > 5){
        $pagination = $app['pagination'](count($companies), $page);
        $companiesPages = $pagination->build();
        $companies = $CategoriaProvider->getCompanies($category, $page);
    }

    if(count($companies) == 0){
        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'companies' => $companies,
            'pages' => $companiesPages,
            'categories' => $categories,
            'companies_list' => true,
            'category_list' => true,
            'category_id' => $id,
            'category_empty_list' => true
        ));
    } else {
        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'companies' => $companies,
            'pages' => $companiesPages,
            'categories' => $categories,
            'companies_list' => true,
            'category_list' => true,
            'category_id' => $id
        ));
    }
})->bind('companies-category-list')->value('page', 1);

/*** Búsqueda avanzada ***/

$app->post('companies/custom/search', function (Request $request) use ($app) {
    $criteria = array();

    $category = $request->request->get('category');
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
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'students' => $students,
        'knowledges' => $knowledges,
        'studies' => $studies,
        'company_custom_list' => true
    ));
})->bind('company-custom-list');

/*** Operaciones CRUD ***/

$app->put('company/create', function (Request $request) use ($app) {
    $EmpresaProvider = new EmpresaProvider($app);

    $cif = $request->request->get("cif");
    $name = $request->request->get("nombre");
    $address = $request->request->get("direccion");
    $cp = $request->request->get("cp");
    if($cp == '') $cp = $request->request->get("small_cp");
    $tlf = $request->request->get("telefono");
    $email = $request->request->get("email");
    if($email == '') $email = $request->request->get("small_email");
    $contacto = $request->request->get("contacto");

    $company = new Empresa($cif, $name, $address, $tlf, $email, $cp, $contacto);
    $EmpresaProvider->createEmpresa($company);

    return $app['twig']->render('forms/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'form_type' => 'edit',
        'company' => $company,
        'new_company' => true
    ));
})->bind('company-new');

$app->get('company/{id}/edit', function ($id) use ($app) {
    $company = null;
    $categories = null;
    if($id != 0){
        $form_type = 'edit';
        $EmpresaProvider = new EmpresaProvider($app);
        $company = $EmpresaProvider->getEmpresa($id);
    } else $form_type = 'new';

    return $app['twig']->render('forms/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'form_type' => $form_type,
        'company' => $company
    ));
})->bind('company-edit');

$app->post('company/{id}/update', function (Request $request, $id) use ($app) {
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $company->setCIF($request->request->get('cif'));
    $company->setDenominacion($request->request->get('nombre'));
    $company->setDireccion($request->request->get('direccion'));
    $company->setCP($request->request->get('cp'));
    $company->setTelefono($request->request->get('telefono'));
    $company->setEmail($request->request->get('email'));
    $company->setContacto($request->request->get('contacto'));
    $EmpresaProvider->updateEmpresa($company);

    return $app['twig']->render('forms/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'form_type' => 'edit',
        'company' => $company,
        'update_company' => true
    ));
})->bind('company-update');

$app->delete('company/{id}/remove', function ($id) use ($app) {
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $EmpresaProvider->removeEmpresa($company);

    return new Response(200);
})->bind('company-remove');

/*** Asociación de entidades ***/

/*** Categorías *///Listados

$app->get('company/{id}/categories/{page}', function ($id, $page) use ($app) {
    $categoriesPages = null;

    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();
    foreach($categories as $i => $value){
        if($company->getActividades()->contains($value))
            unset($categories[$i]);
    }
    if(count($categories) > 5){
        $pagination = $app['pagination'](count($categories), $page);
        $categoriesPages = $pagination->build();
        $categories = $CategoriaProvider->getCategorias($page);
    }

    return $app['twig']->render('additions/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'company' => $company,
        'categories' => $categories,
        'pages' => $categoriesPages,
        'company_categories_list' => true
    ));
})->bind('company-add-categories')->value('page', 1);

/*** Categorías *///Adición

$app->get('company/{id}/category/{category_id}/add', function ($id, $category_id) use ($app) {
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $CategoryProvider = new CategoriaActividadProvider($app);
    $category = $CategoryProvider->getCategoria($category_id);

    $company->addActivity($category);
    $EmpresaProvider->updateEmpresa($company);

    return new Response(200);
})->bind('company-add-category');

/*** Categorías *///Eliminación

$app->delete('company/{id}/category/{category_id}/remove', function ($id, $category_id) use ($app) {
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $CategoriaProvider = new CategoriaActividadProvider($app);
    $category = $CategoriaProvider->getCategoria($category_id);

    $company->removeActivity($category);
    $EmpresaProvider->updateEmpresa($company);

    return new Response(200);
})->bind('company-remove-category');

/*** Alumnos *///Listados

$app->get('company/{id}/students/{page}', function ($id, $page) use ($app) {
    $studentsPages = null;

    $EstudioProvider = new EstudioProvider($app);
    $studies = $EstudioProvider->getEstudios();
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();


    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $companies = $EmpresaProvider->getEmpresas();

    $AlumnoProvider = new AlumnoProvider($app);
    $students = $AlumnoProvider->getAlumnos();
    foreach($students as $i => $value){
        if($company->getAlumnos()->contains($value))
            unset($students[$i]);
    }
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $AlumnoProvider->getAlumnos($page);
    }

    return $app['twig']->render('additions/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'company' => $company,
        'students' => $students,
        'pages' => $studentsPages,
        'studies' => $studies,
        'companies' => $companies,
        'knowledges' => $knowledges,
        'company_students_list' => true
    ));
})->bind('add-company-students')->value('page', 1);

$app->get('company/{id}/students/study/{study_id}/{page}', function ($id, $study_id, $page) use ($app) {
    $studentsPages = null;

    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $companies = $EmpresaProvider->getEmpresas();

    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();

    $AlumnoProvider = new AlumnoProvider($app);
    $students = $AlumnoProvider->getAlumnosBy(array('study' => $study_id));
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $AlumnoProvider->getAlumnosBy(array('study' => $study_id), $page);
    }

    return $app['twig']->render('additions/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'company' => $company,
        'students' => $students,
        'pages' => $studentsPages,
        'study_id' => $study_id,
        'studies' => $studies,
        'companies' => $companies,
        'knowledges' => $knowledges,
        'company_study_students_list' => true
    ));
})->bind('add-company-students-study')->value('page', 1);

$app->get('company/{id}/students/knowledge/{knowledge_id}/{page}', function ($id, $knowledge_id, $page) use ($app) {
    $studentsPages = null;

    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $companies = $EmpresaProvider->getEmpresas();

    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();

    $AlumnoProvider = new AlumnoProvider($app);
    $students = $AlumnoProvider->getAlumnosBy(array('knowledge' => $knowledge_id));
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $AlumnoProvider->getAlumnosBy(array('knowledge' => $knowledge_id), $page);
    }

    return $app['twig']->render('additions/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'company' => $company,
        'students' => $students,
        'pages' => $studentsPages,
        'knowledge_id' => $knowledge_id,
        'studies' => $studies,
        'companies' => $companies,
        'knowledges' => $knowledges,
        'company_study_students_list' => true
    ));
})->bind('add-company-students-knowledge')->value('page', 1);

$app->get('company/{id}/students/company/{company_id}/{page}', function ($id, $company_id, $page) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    $studentsPages = null;

    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($id);
    $companies = $EmpresaProvider->getEmpresas();

    $EstudiosProvider = new EstudioProvider($app);
    $studies = $EstudiosProvider->getEstudios();
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledges = $ConocimientoProvider->getConocimientos();

    $AlumnoProvider = new AlumnoProvider($app);
    $students = $AlumnoProvider->getAlumnosBy(array('company' => $company_id));
    if(count($students) > 5){
        $pagination = $app['pagination'](count($students), $page);
        $studentsPages = $pagination->build();
        $students = $AlumnoProvider->getAlumnosBy(array('company' => $company_id), $page);
    }

    return $app['twig']->render('additions/company.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'user' => $user,
        'company' => $company,
        'students' => $students,
        'pages' => $studentsPages,
        'company_id' => $company_id,
        'studies' => $studies,
        'companies' => $companies,
        'knowledges' => $knowledges,
        'company_study_students_list' => true
    ));
})->bind('add-company-students-company')->value('page', 1);

/*** Alumnos *///Adición

$app->get('company/{id}/student/{student_id}/add', function ($id, $student_id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $EmpresaProvider = new EmpresaProvider($app);
        $company = $EmpresaProvider->getCompany($id);
        $AlumnoProvider = new AlumnoProvider($app);
        $student = $AlumnoProvider->getAlumno($student_id);

        $company->addAlumno($student);
        $EmpresaProvider->updateCompany($company);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('company-add-student');

/*** Alumnos *///Eliminación

$app->delete('student/{id}/study/{study_id}/remove', function ($id, $study_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EstudioProvider = new EstudioProvider($app);
    $study = $EstudioProvider->getEstudio($study_id);

    $student->removeStudy($study);
    $AlumnoProvider->updateAlumno($student);

    return new Response(200);
})->bind('company-remove-student');