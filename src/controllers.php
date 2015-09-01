<?php

use Lib\Providers\AlumnoProvider;
use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\ConocimientoProvider;
use Lib\Providers\EmpresaProvider;
use Lib\Providers\EstudioProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));
Request::enableHttpMethodParameterOverride(); //Habilitación de métodos alternativos de HTTP (PUT, DELETE)

/*** Ruta principal ***/

$app->get('/', function () use ($app) {
    //Generación de esquema de BD (si no existe)
    try {
        //Test de conexión con la BD
        $em = $app['orm.em'];
        $em->getConnection()->connect();
    } catch (\Exception $e) {
        $console = require 'console.php';
        $console->setHelperSet(new Symfony\Component\Console\Helper\HelperSet(array(
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($app["db"]),
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($app["orm.em"])
        )));
        $console->addCommands(array(
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand,
        ));

        $command = $console->find('orm:schema-tool:create');
        $command->run(new ArrayInput(array('')), new NullOutput());
    }

    return $app['twig']->render('index.html.twig', array(
        'domain' => $GLOBALS['MAILING_DOMAIN']
    ));
})->bind('home');

/*** Ruta de obtención de datos para el dashboard ***/

$app->get('/data', function () use ($app) {
    //Generación de datos de estudios
    $EstudioProvider = new EstudioProvider($app);
    $studies = $EstudioProvider->getEstudios();
    $studiesData = array();
    foreach ($studies as $study) {
        $studiesData[] = array($study->getDenominacion());
    }
    $studiesData[] = count($EstudioProvider->getEstudios());

    //Generación de datos de estudios por categoría
    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();
    $studiesByCategory = array();
    foreach ($categories as $category) {
        $studiesByCategory[] = array(
            $category->getDenominacion(),
            count($CategoriaProvider->getStudies($category))
        );
    }

    //Generación de datos de estudiantes por estudios
    $AlumnoProvider = new AlumnoProvider($app);
    $studentsTotal = count($AlumnoProvider->getAlumnos());
    foreach ($studies as $study) {
        $studentsByStudy[] = array(
            $study->getDenominacion(),
            (count($EstudioProvider->getAlumnos($study)) / $studentsTotal) * $studentsTotal
        );
    }
    $studentsByStudy[] = $studentsTotal;

    //Generación de datos de estudiantes por conocimientos
    $ConocimientoProvider = new ConocimientoProvider($app);
    $studentsByKnowledge = array();
    foreach ($ConocimientoProvider->getConocimientos() as $knowledge) {
        $studentsByKnowledge[] = array(
            $knowledge->getDenominacion(),
            (count($ConocimientoProvider->getAlumnos($knowledge)) / $studentsTotal) * $studentsTotal
        );
    }

    //Generación de datos de empresas por actividad o categoría
    $EmpresaProvider = new EmpresaProvider($app);
    $companiesTotal = count($EmpresaProvider->getEmpresas());
    foreach ($categories as $category) {
        $companiesByCategory[] = array(
            $category->getDenominacion(),
            (count($CategoriaProvider->getCompanies($category)) / $companiesTotal) * $companiesTotal
        );
    }
    $companiesByCategory[] = $companiesTotal;

    return new JsonResponse(array(
        'studies'  => $studiesData,
        'categorizedStudies' => $studiesByCategory,
        'studentsByStudy' => $studentsByStudy,
        'studentsByKnowledge' => $studentsByKnowledge,
        'companies' => $companiesByCategory));
});

require __DIR__.'/controllers/UsersController.php'; //Controlador de rutas de usuario
require __DIR__.'/controllers/StudentsController.php'; //Controlador de rutas de alumnos
require __DIR__.'/controllers/CompaniesController.php'; //Controlador de rutas de empresas
require __DIR__.'/controllers/StudiesController.php'; //Controlador de rutas de estudios
require __DIR__.'/controllers/KnowledgesController.php'; //Controlador de rutas de conocimientos
require __DIR__.'/controllers/CategoriesController.php'; //Controlador de rutas de categorías

/*** Búsqueda por categoría ***/

$app->post('category/search', function (Request $request) use ($app) {
    $listType = $request->request->get('listType');
    $categoryId = $request->request->get('category');

    switch($listType){
        case 'student':
            $studentId = $request->request->get('student');
            $searchType = $request->request->get('searchType');

            switch($searchType){
                case 'studies':
                    return $app->redirect($app['url_generator']->generate('add-categorized-studies', array(
                        'id' => $studentId,
                        'category_id' => $categoryId
                    )));
                    break;
                case 'knowledges':
                    return $app->redirect($app['url_generator']->generate('add-categorized-knowledges', array(
                        'id' => $studentId,
                        'category_id' => $categoryId
                    )));
                    break;
                case 'companies':
                    return $app->redirect($app['url_generator']->generate('add-categorized-companies', array(
                        'id' => $studentId,
                        'category_id' => $categoryId
                    )));
            }
            break;
        case 'companies':
            return $app->redirect($app['url_generator']->generate('companies-category-list', array(
                'id' => $categoryId
            )));
    }
})->bind('category-search');

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
