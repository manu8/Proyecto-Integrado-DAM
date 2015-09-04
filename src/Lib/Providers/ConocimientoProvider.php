<?php

namespace Lib\Providers;


use Entities\HabilidadConocimiento;

class ConocimientoProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array|null Todos los conocimientos registrados
     */
    public function getConocimientos($offset = null)
    {
        $knowledge = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!is_null($offset)){
                $query = $em->createQuery('SELECT c FROM Entities:HabilidadConocimiento c');
                $query->setMaxResults(5);
                $query->setFirstResult(($offset - 1) * 5);
                $knowledge = $query->getResult();
            }
            else $knowledge = $em->getRepository('Entities\HabilidadConocimiento')->findAll();
        }
        return $knowledge;
    }

    /**
     * @param $id Id del conocimiento a buscar
     * @return null|object El conocimiento con el id proporcionado
     */
    public function getConocimiento($id)
    {
        $knowledege = null;
        $em = $this->app['orm.em'];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $knowledege = $em->getRepository('Entities\HabilidadConocimiento')->findOneBy(array(
                'Id' => $id
            ));
        }
        return $knowledege;
    }

    public function getAlumnos(HabilidadConocimiento $knowledge, $offset = null)
    {
        $em = $this->app['orm.em'];
        if(!is_null($offset)){
            $qb = $em->createQueryBuilder();
            $qb->select('a')
                ->from('Entities:Alumno', 'a')
                ->leftJoin('a.ConocimientosHabilidades', 'e')
                ->where('e = :knowledge')
                ->setParameter('knowledge', $knowledge);
            $query = $qb->getQuery();
            $query->setMaxResults(5);
            $query->setFirstResult(($offset - 1) * 5);
            $students = $query->getResult();
        } else $students = $knowledge->getAlumnos();
        return $students;
    }

    /**
     * Elimina un conocimiento existente de la base de datos
     * @param HabilidadConocimiento $knowledge Conocimiento a eliminar de la BD
     */
    public function removeConocimiento(HabilidadConocimiento $knowledge)
    {
        $em = $this->app['orm.em'];
        $em->remove($knowledge);
        $em->flush();
    }
}