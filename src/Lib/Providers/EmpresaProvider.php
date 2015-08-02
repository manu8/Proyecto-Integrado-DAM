<?php

namespace Lib\Providers;

use Entities\Empresa;

class EmpresaProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param null $offset Número de página (paginación)
     * @return array|null Todos las empresas registradas
     */
    public function getEmpresas($offset = null)
    {
        $companies = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!is_null($offset)){
                $query = $em->createQuery('SELECT e FROM Entities:Empresa e');
                $query->setMaxResults(5);
                $query->setFirstResult(($offset - 1) * 5);
                $companies = $query->getResult();
            }
            else $companies = $em->getRepository('Entities\Empresa')->findAll();
        }
        return $companies;
    }

    /**
     * @param $id Id de la empresa a buscar
     * @return null|object La empresa con el id proporcionado
     */
    public function getEmpresa($id)
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

    /**
     * Crea una nueva empresa en la base de datos
     * @param Empresa $company Empresa a almacenar en la BD
     */
    public function createEmpresa(Empresa $company)
    {
        $em = $this->app['orm.em'];
        $em->persist($company);
        $em->flush();
    }

    /**
     * Modifica una empresa existente en la base de datos
     * @param Empresa $company Empresa a modificar en la BD
     */
    public function updateEmpresa(Empresa $company)
    {
        $em = $this->app['orm.em'];
        $em->persist($company);
        $em->flush();
    }

    /**
     * Elimina una empresa existente de la base de datos
     * @param Empresa $company Empresa a eliminar de la BD
     */
    public function removeEmpresa(Empresa $company)
    {
        $em = $this->app['orm.em'];
        $em->remove($company);
        $em->flush();
    }
}