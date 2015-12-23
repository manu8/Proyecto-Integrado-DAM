<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\AlumnoProvider;
use Lib\Providers\EstudioProvider;
use Lib\Providers\ConocimientoProvider;
use Lib\Providers\EmpresaProvider;
use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\UserProvider;

use Entities\Alumno;

/*** Listados ***/

$app->get('students/list/{page}', function ($page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();
        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnos();
        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $AlumnoProvider->getAlumnos($page);
        }

        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'students' => $students,
            'pages' => $studentsPages,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'companies' => $companies,
            'students_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('students-list')->value('page', 1);

$app->get('students/study/{id}/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();
        $EstudioProvider = new EstudioProvider($app);
        $studies = $EstudioProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $study = $EstudioProvider->getEstudio($id);
        $students = $EstudioProvider->getAlumnos($study);
        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $EstudioProvider->getAlumnos($study, $page);
        }

        if(count($students) == 0){
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'students' => $students,
                'pages' => $studentsPages,
                'knowledges' => $knowledges,
                'studies' => $studies,
                'companies' => $companies,
                'students_list' => true,
                'study_list' => true,
                'study_id' => $id,
                'study_empty_list' => true
            ));
        } else {
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'students' => $students,
                'pages' => $studentsPages,
                'knowledges' => $knowledges,
                'studies' => $studies,
                'companies' => $companies,
                'students_list' => true,
                'study_list' => true,
                'study_id' => $id
            ));
        }
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('students-study-list')->value('page', 1);

$app->get('students/knowledge/{id}/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();
        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $students = $ConocimientoProvider->getAlumnos($knowledge);
        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $ConocimientoProvider->getAlumnos($knowledge, $page);
        }

        if(count($students) == 0){
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'students' => $students,
                'pages' => $studentsPages,
                'knowledges' => $knowledges,
                'studies' => $studies,
                'companies' => $companies,
                'students_list' => true,
                'knowledge_list' => true,
                'knowledge_id' => $id,
                'knowledge_empty_list' => true
            ));
        }
        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'students' => $students,
            'pages' => $studentsPages,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'companies' => $companies,
            'students_list' => true,
            'knowledge_list' => true,
            'knowledge_id' => $id
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('students-knowledge-list')->value('page', 1);

$app->get('students/company/{id}/{page}', function ($id, $page) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $studentsPages = null;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();
        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $company = $EmpresaProvider->getEmpresa($id);
        $students = $EmpresaProvider->getAlumnos($company);
        if(count($students) > 5){
            $pagination = $app['pagination'](count($students), $page);
            $studentsPages = $pagination->build();
            $students = $EmpresaProvider->getAlumnos($company, $page);
        }

        if(count($students) == 0){
            return $app['twig']->render('list-wrapper.html.twig', array(
                'domain' => $GLOBALS['MAILING_DOMAIN'],
                'students' => $students,
                'pages' => $studentsPages,
                'knowledges' => $knowledges,
                'studies' => $studies,
                'companies' => $companies,
                'students_list' => true,
                'company_list' => true,
                'company_id' => $id,
                'company_empty_list' => true
            ));
        }
        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'students' => $students,
            'pages' => $studentsPages,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'companies' => $companies,
            'students_list' => true,
            'company_list' => true,
            'company_id' => $id
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('students-company-list')->value('page', 1);


/*** Búsquedas ***/

$app->post('students/study/search', function (Request $request) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $listType = $request->request->get('listType');
        $id = $request->request->get('studySelect');
        switch($listType){
            case 'company':
                $companyId = $request->request->get('company');

                return $app->redirect($app['url_generator']->generate('add-company-students-study', array(
                    'id' => $companyId,
                    'study_id' => $id
                )));
                break;
            case 'study':
                $studyId = $request->request->get('study');

                return $app->redirect($app['url_generator']->generate('add-study-students-study', array(
                    'id' => $studyId,
                    'study_id' => $id
                )));
                break;
            case 'knowledge':
                $knowledgeId = $request->request->get('knowledge');

                return $app->redirect($app['url_generator']->generate('add-knowledge-students-study', array(
                    'id' => $knowledgeId,
                    'study_id' => $id
                )));
                break;
            case 'student':
                return $app->redirect($app['url_generator']->generate('students-study-list', array('id' => $id)));
        }
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('study-search');

$app->post('students/company/search', function (Request $request) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $listType = $request->request->get('listType');
        $id = $request->request->get('companySelect');
        switch($listType){
            case 'company':
                $companyId = $request->request->get('company');

                return $app->redirect($app['url_generator']->generate('add-company-students-company', array(
                    'id' => $companyId,
                    'company_id' => $id
                )));
                break;
            case 'study':
                $studyId = $request->request->get('study');

                return $app->redirect($app['url_generator']->generate('add-study-students-company', array(
                    'id' => $studyId,
                    'company_id' => $id
                )));
                break;
            case 'knowledge':
                $knowledgeId = $request->request->get('knowledge');

                return $app->redirect($app['url_generator']->generate('add-knowledge-students-study', array(
                    'id' => $knowledgeId,
                    'study_id' => $id
                )));
                break;
            case 'student':
                return $app->redirect($app['url_generator']->generate('students-study-list', array('id' => $id)));
        }
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('company-search');

$app->post('students/knowledge/search', function (Request $request) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $listType = $request->request->get('listType');
        $id = $request->request->get('knowledgeSelect');
        switch($listType) {
            case 'company':
                $companyId = $request->request->get('company');

                return $app->redirect($app['url_generator']->generate('add-company-students-knowledge', array(
                    'id' => $companyId,
                    'knowledge_id' => $id
                )));
                break;
            case 'study':
                $studyId = $request->request->get('study');

                return $app->redirect($app['url_generator']->generate('add-study-students-knowledge', array(
                    'id' => $studyId,
                    'knowledge_id' => $id
                )));
                break;
            case 'knowledge':
                $knowledgeId = $request->request->get('knowledge');

                return $app->redirect($app['url_generator']->generate('add-knowledge-students-knowledge', array(
                    'id' => $knowledgeId,
                    'knowledge_id' => $id
                )));
                break;
            case 'student':
                return $app->redirect($app['url_generator']->generate('students-knowledge-list', array('id' => $id)));
        }
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('knowledge-search');

$app->post('students/custom/search', function (Request $request) use ($app) {
    if($app['security.authorization_checker']->isGranted('ROLE_USER')){
        $criteria = array();

        $knowledge = $request->request->get('knowledge');
        $study = $request->request->get('study');
        $company = $request->request->get('company');
        $nif = $request->request->get('nif');
        $name = $request->request->get('nombre');
        $surnames = $request->request->get('apellidos');

        if(!empty($knowledge)) $criteria['knowledge'] = $knowledge;
        if(!empty($study)) $criteria['study'] = $study;
        if(!empty($company)) $criteria['company'] = $company;
        if(!empty($nif)) $criteria['nif'] = $nif;
        if(!empty($name)) $criteria['name'] = $name;
        if(!empty($surnames)) $criteria['surnames'] = $surnames;

        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledges = $ConocimientoProvider->getConocimientos();
        $EstudiosProvider = new EstudioProvider($app);
        $studies = $EstudiosProvider->getEstudios();
        $EmpresaProvider = new EmpresaProvider($app);
        $companies = $EmpresaProvider->getEmpresas();

        $AlumnoProvider = new AlumnoProvider($app);
        $students = $AlumnoProvider->getAlumnosBy($criteria);

        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['MAILING_DOMAIN'],
            'students' => $students,
            'knowledges' => $knowledges,
            'studies' => $studies,
            'companies' => $companies,
            'students_custom_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));
})->bind('students-custom-list');

/*** Operaciones CRUD ***/

$app->put('student/create', function (Request $request) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);

    $nif = $request->request->get("nif");
    $name = $request->request->get("nombre");
    $surnames = $request->request->get("apellidos");
    if($surnames == '') $surnames = $request->request->get("small_apellidos");
    $address = $request->request->get("direccion");
    $cp = $request->request->get("cp");
    if($cp == '') $cp = $request->request->get("small_cp");
    $tlf = $request->request->get("telefono");
    $email = $request->request->get("email");

    $student = new Alumno($nif, $name, $surnames, $address, $cp, $tlf, $email);
    $AlumnoProvider->createAlumno($student);

    return $app['twig']->render('forms/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'form_type' => 'edit',
        'student' => $student,
        'new_student' => true
    ));
})->bind('student-new');

$app->get('student/{id}/edit', function ($id) use ($app) {
    $student = null;
    if($id != 0){
        $form_type = 'edit';
        $AlumnoProvider = new AlumnoProvider($app);
        $student = $AlumnoProvider->getAlumno($id);
    } else $form_type = 'new';

    return $app['twig']->render('forms/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'form_type' => $form_type,
        'student' => $student
    ));
})->bind('student-edit');

$app->post('student/{id}/update', function (Request $request, $id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $nif = $request->request->get('nif');
    $name = $request->request->get('nombre');
    $surnames = $request->request->get('apellidos');
    if(!is_null($surnames)) $surnames = $request->request->get('small_apellidos');
    $address = $request->request->get('direccion');
    $cp = $request->request->get('cp');
    if(!is_null($cp)) $request->request->get('small_cp');
    $tlf = $request->request->get('telefono');
    $email = $request->request->get('email');
    if(!is_null($email)) $request->request->get('small_email');

    $student->setNIF($nif);
    $student->setNombre($name);
    $student->setApellidos($surnames);
    $student->setDireccion($address);
    $student->setCP($cp);
    $student->setTelefono($tlf);
    $student->setEmail($email);

    $AlumnoProvider->updateAlumno($student);

    return $app['twig']->render('forms/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'form_type' => 'edit',
        'student' => $student,
        'update_student' => true
    ));
})->bind('student-update');

$app->delete('student/{id}/remove', function ($id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $AlumnoProvider->removeAlumno($student);

    return new Response(200);
})->bind('student-remove');

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

    return $app['twig']->render('additions/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'student' => $student,
        'studies' => $studies,
        'categories' => $categories,
        'pages' => $studiesPages,
        'student_studies_list' => true
    ));
})->bind('add-studies')->value('page', 1);

$app->get('student/{id}/studies/category/{category_id}/{page}', function ($id, $category_id, $page) use ($app) {
    $studiesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $category = $CategoriaProvider->getCategoria($category_id);
    $studies = $CategoriaProvider->getStudies($category);
    foreach($studies as $i => $value){
        if($student->getEstudiosTitulos()->contains($value))
            unset($studies[$i]);
    }
    if(count($studies) > 5){
        $pagination = $app['pagination'](count($studies), $page);
        $studiesPages = $pagination->build();
        $studies = $CategoriaProvider->getStudies($category, $page);
    }

    $categories = $CategoriaProvider->getCategorias();

    return $app['twig']->render('additions/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'student' => $student,
        'categories' => $categories,
        'studies' => $studies,
        'pages' => $studiesPages,
        'category_id' => $category_id,
        'student_studies_list' => true,
        'student_categorized_studies_list' => true
    ));
})->bind('add-categorized-studies')->value('page', 1);

/*** Estudios *///Adición

$app->get('student/{id}/study/{study_id}/add', function ($id, $study_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EstudioProvider = new EstudioProvider($app);
    $study = $EstudioProvider->getEstudio($study_id);

    $student->addStudy($study);
    $em = $app['orm.em'];
    $em->flush();

    return new Response(200);
})->bind('add-study');

/*** Estudios *///Eliminación

$app->delete('student/{id}/study/{study_id}/remove', function ($id, $study_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EstudioProvider = new EstudioProvider($app);
    $study = $EstudioProvider->getEstudio($study_id);

    $student->removeStudy($study);
    $em = $app['orm.em'];
    $em->flush();

    return new Response(200);
})->bind('remove-student-study');

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

    return $app['twig']->render('additions/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'student' => $student,
        'knowledges' => $knowledges,
        'pages' => $knowledgesPages,
        'categories' => $categories,
        'student_knowledges_list' => true
    ));

})->bind('add-knowledges')->value('page', 1);

$app->get('student/{id}/knowledges/category/{category_id}/{page}', function ($id, $category_id, $page) use ($app) {
    $knowledgesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $category = $CategoriaProvider->getCategoria($category_id);
    $knowledges = $CategoriaProvider->getKnowledges($category);
    if(count($knowledges) > 5){
        $pagination = $app['pagination'](count($knowledges), $page);
        $knowledgesPages = $pagination->build();
        $knowledges = $CategoriaProvider->getKnowledges($category, $page);
    }

    $categories = $CategoriaProvider->getCategorias();

    return $app['twig']->render('additions/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'student' => $student,
        'categories' => $categories,
        'knowledges' => $knowledges,
        'pages' => $knowledgesPages,
        'category_id' => $category_id,
        'student_knowledges_list' => true,
        'student_categorized_knowledges_list' => true
    ));
})->bind('add-categorized-knowledges')->value('page', 1);

/*** Conocimientos *///Adición

$app->get('student/{id}/knowledge/{knowledge_id}/add', function ($id, $knowledge_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledge = $ConocimientoProvider->getConocimiento($knowledge_id);

    $student->addKnowledge($knowledge);
    $em = $app['orm.em'];
    $em->flush();

    return new Response(200);
})->bind('add-knowledge');

/*** Conocimientos *///Eliminación

$app->delete('student/{id}/knowledge/{knowledge_id}/remove', function ($id, $knowledge_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $ConocimientoProvider = new ConocimientoProvider($app);
    $knowledge = $ConocimientoProvider->getConocimiento($knowledge_id);

    $student->removeKnowledge($knowledge);
    $em = $app['orm.em'];
    $em->flush();

    return new Response(200);
})->bind('remove-student-knowledge');

/*** Empresas *///Listados

$app->get('student/{id}/companies/{page}', function ($id, $page) use ($app) {
    $companiesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $EmpresaProvider = new EmpresaProvider($app);
    $companies = $EmpresaProvider->getEmpresas();
    foreach($companies as $i => $value){
        if($student->getEmpresas()->contains($value))
            unset($companies[$i]);
    }
    if(count($companies) > 5){
        $pagination = $app['pagination'](count($companies), $page);
        $companiesPages = $pagination->build();
    }

    return $app['twig']->render('additions/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'student' => $student,
        'companies' => $companies,
        'pages' => $companiesPages,
        'categories' => $categories,
        'student_companies_list' => true
    ));

})->bind('add-companies')->value('page', 1);

$app->get('student/{id}/companies/category/{category_id}/{page}', function ($id, $category_id, $page) use ($app) {
    $companiesPages = null;

    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $category = $CategoriaProvider->getCategoria($category_id);
    $companies = $CategoriaProvider->getCompanies($category);
    if(count($companies) > 5){
        $pagination = $app['pagination'](count($companies), $page);
        $companiesPages = $pagination->build();
        $companies = $companies = $CategoriaProvider->getCompanies($category, $page);
    }

    $categories = $CategoriaProvider->getCategorias();

    return $app['twig']->render('additions/student.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN'],
        'student' => $student,
        'categories' => $categories,
        'companies' => $companies,
        'pages' => $companiesPages,
        'category_id' => $category_id,
        'student_categorized_companies_list' => true
    ));
})->bind('add-categorized-companies')->value('page', 1);

/*** Empresas *///Adición

$app->get('student/{id}/company/{company_id}/add', function ($id, $company_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($company_id);

    $student->addCompany($company);
    $em = $app['orm.em'];
    $em->flush();

    return new Response(200);
})->bind('add-company');

/*** Empresas *///Eliminación

$app->delete('student/{id}/company/{company_id}/remove', function ($id, $company_id) use ($app) {
    $AlumnoProvider = new AlumnoProvider($app);
    $student = $AlumnoProvider->getAlumno($id);
    $EmpresaProvider = new EmpresaProvider($app);
    $company = $EmpresaProvider->getEmpresa($company_id);

    $student->removeCompany($company);
    $em = $app['orm.em'];
    $em->flush();

    return new Response(200);
})->bind('remove-student-company');