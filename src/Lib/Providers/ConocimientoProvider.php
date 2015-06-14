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
    public function getConocimientos()
    {
        $studies = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $studies = $em->getRepository('Entities\HabilidadConocimiento')->findAll();
        }
        return $studies;
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
}