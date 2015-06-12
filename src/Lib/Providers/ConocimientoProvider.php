<?php

namespace Lib\Providers;


class ConocimientoProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array|null Todos los conocimientos registrados
     */
    public function getConocimientos()
    {
        $studies = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $studies = $em->getRepository('Entities\HabilidadConocimiento')->findAll();
        }
        return $studies;
    }
}