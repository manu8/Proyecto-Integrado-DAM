<?php

namespace Lib\Providers;


class EstudioProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array|null Todos los estudios registrados
     */
    public function getEstudios()
    {
        $studies = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $studies = $em->getRepository('Entities\EstudioTitulo')->findAll();
        }
        return $studies;
    }
}