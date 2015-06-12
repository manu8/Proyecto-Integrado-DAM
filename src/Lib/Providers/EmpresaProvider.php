<?php

namespace Lib\Providers;


class EmpresaProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array|null Todos las empresas registradas
     */
    public function getCompanies()
    {
        $companies = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $companies = $em->getRepository('Entities\Empresa')->findAll();
        }
        return $companies;
    }
}