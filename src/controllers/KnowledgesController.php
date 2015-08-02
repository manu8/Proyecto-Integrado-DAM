<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lib\Providers\ConocimientoProvider;
use Lib\Providers\CategoriaActividadProvider;
use Lib\Providers\AlumnoProvider;
use Lib\Providers\UserProvider;

/*** Listados ***/

$app->get('knowledges/list/{page}', function ($page) use ($app) {
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
        'domain' => $GLOBALS['DOMAIN'],
        'knowledges' => $knowleges,
        'pages' => $knowledgesPages,
        'categories' => $categories,
        'knowledges_list' => true
    ));
})->bind('knowledges_list')->value('page', 1);

$app->get('knowledeges/category/{id}/{page}', function ($id, $page) use ($app) {
    $knowledgesPages = null;

    $CategoriaProvider = new CategoriaActividadProvider($app);
    $categories = $CategoriaProvider->getCategorias();

    $category = $CategoriaProvider->getCategoria($id);
    $knowledges = $CategoriaProvider->getKnowledges($category);
    if(count($knowledges) > 5){
        $pagination = $app['pagination'](count($knowledges), $page);
        $knowledgesPages = $pagination->build();
        $knowledges = $CategoriaProvider->getKnowledges($category, $page);
    }

    if(count($knowledges) == 0){
        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'knowledges' => $knowledges,
            'pages' => $knowledgesPages,
            'categories' => $categories,
            'category_list' => true,
            'category_id' => $id,
            'category_empty_list' => true
        ));
    } else {
        return $app['twig']->render('list-wrapper.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'knowledges' => $knowledges,
            'pages' => $knowledgesPages,
            'categories' => $categories,
            'category_list' => true,
            'category_id' => $id
        ));
    }
})->bind('knowledges_category_list')->value('page', 1);

/*** Operaciones CRUD ***/

$app->get('knowledge/{id}/edit', function ($id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $knowledge = null;
        $categories = null;
        if($id != 0){
            $form_type = 'edit';
            $ConocimientoProvider = new ConocimientoProvider($app);
            $knowledge = $ConocimientoProvider->getConocimiento($id);
        } else $form_type = 'new';

        return $app['twig']->render('forms/knowledge.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'user' => $user,
            'form_type' => $form_type,
            'knowledge' => $knowledge
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('knowledge_edit');

$app->post('knowledge/{id}/update', function (Request $request, $id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $knowledge->setDenominacion($request->request->get('nombre'));
        $knowledge->setDescripcion($request->request->get('descripcion'));
        $ConocimientoProvider->updateConocimiento($knowledge);

        return $app['twig']->render('forms/company.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'user' => $user,
            'form_type' => 'edit',
            'knowledge' => $knowledge,
            'update_knowledge' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('/login'));

})->bind('knowledge_update');

$app->delete('knowledge/{id}/remove', function ($id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $ConocimientoProvider->removeConocimiento($knowledge);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('knowledge_remove');

/*** Asociación de entidades ***/

/*** Categorías *///Listados

$app->get('knowledge/{id}/categories/{page}', function ($id, $page) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
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

        return $app['twig']->render('additions/category-addition.html.twig', array(
            'domain' => $GLOBALS['DOMAIN'],
            'user' => $user,
            'knowledge' => $knowledge,
            'categories' => $categories,
            'pages' => $categoriesPages,
            'knowledge_categories_list' => true
        ));
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('knowledge_add_categories')->value('page', 1);

/*** Categorías *///Adición

$app->get('knowledge/{id}/category/{category_id}/add', function ($id, $category_id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($category_id);

        $knowledge->addCategory($category);
        $ConocimientoProvider->updateConocimiento($knowledge);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('knowledge_add_category');

/*** Categorías *///Eliminación

$app->delete('knowledge/{id}/category/{category_id}/remove', function ($id, $category_id) use ($app) {
    $UserProvider = new UserProvider($app);
    $user = $UserProvider->getCurrentUser();

    if(!is_null($user)){
        $ConocimientoProvider = new ConocimientoProvider($app);
        $knowledge = $ConocimientoProvider->getConocimiento($id);
        $CategoriaProvider = new CategoriaActividadProvider($app);
        $category = $CategoriaProvider->getCategoria($category_id);

        $knowledge->removeCategory($category);
        $ConocimientoProvider->updateConocimiento($knowledge);

        return new Response(200);
    } else return $app->redirect($app['url_generator']->generate('login'));

})->bind('knowledge_remove_category');