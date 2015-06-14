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

    /**
     * @param $id Id de la empresa a buscar
     * @return null|object La empresa con el id proporcionado
     */
    public function getCompany($id)
    {
        $company = null;
        $em = $this->app['orm.em'];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $company = $em->getRepository('Entities\Empresa')->findOneBy(array(
                'Id' => $id
            ));
        }
        return $company;
    }
}