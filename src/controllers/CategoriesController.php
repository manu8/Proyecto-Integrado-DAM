<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\UserProvider;

/*** Listados ***/

$app->get('categories/list/{page}', function ($page) use ($app) {
    $categoriesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    if(count($categories) > 5){
        $pagination = $app['pagination'](count($categories), $page);
        $categoriesPages = $pagination->build();
        $categories = $CategoriaProvider->getCategorias($page);
    }

    return $app['twig']->render('list-wrapper.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'pages' => $categoriesPages,
        'categories' => $categories,
        'categories_list' => true
    ));
})->bind('categories_list')->value('page', 1);

/*** Operaciones CRUD ***/

$app->get('category/{id}/edit', function ($id) use ($app) {
    $category = null;
    $category = null;
    if($id != 0){
        $form_type = 'edit';
        $CategoryProvider = new CategoriaActividadProvider($app);
        $category = $CategoryProvider->getCategoria($id);
    } else $form_type = 'new';

    return $app['twig']->render('forms/knowledge.html.twig', array(
        'domain' => $GLOBALS['DOMAIN'],
        'form_type' => $form_type,
        'category' => $category
    ));
})->bind('category_edit');

$app->post('category/{id}/update', function (Request $request, $id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($id);
        $category->setDenominacion($request->request->get('nombre'));
        $category->setContacto($request->request->get('descripcion'));
        $CategoriaProvider->updateCategoria($category);

        return $app['twig']->render('forms/category.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'form_type' => 'edit',
            'category' => $category,
            'update_category' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('category_update');

$app->delete('category/{id}/remove', function ($id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($id);
        $CategoriaProvider->removeCategoria($category);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('category_remove');