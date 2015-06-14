<?php

namespace Lib\Providers;


class CategoriaActividadProvider {

    /**
     * @var \Silex\Application
     */
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array|null Todas las categorías existentes
     */
    public function getCategorias()
    {
        $categories = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $categories = $em->getRepository('Entities\CategoriaActividad')->findAll();
        }
        return $categories;
    }
}