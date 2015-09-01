<?php

namespace Lib\Providers;

use Entities\EstudioTitulo;

class EstudioProvider {

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param null $offset Índice de primer item del resultado
     * @return array|null Todos los estudios registrados
     */
    public function getEstudios($offset = null)
    {
        $studies = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!is_null($offset)) {
                $query = $em->createQuery('SELECT e FROM Entities:EstudioTitulo e');
                $query->setMaxResults(5);
                $query->setFirstResult(($offset - 1) * 5);
                $studies = $query->getResult();
            } else $studies = $em->getRepository('Entities\EstudioTitulo')->findAll();
        }
        return $studies;
    }

    /**
     * @param $id Id del estudio a buscar
     * @return null|object Estudio con el id indicado
     */
    public function getEstudio($id)
    {
        $study = null;
        $em = $this->app['orm.em'];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $study = $em->getRepository('Entities\EstudioTitulo')->findOneBy(array(
                'Id' => $id
            ));
        }
        return $study;
    }

    public function getAlumnos(EstudioTitulo $study, $offset = null)
    {
        $em = $this->app['orm.em'];
        if(!is_null($offset)){
            $qb = $em->createQueryBuilder();
            $qb->select('a')
                ->from('Entities:Alumno', 'a')
                ->leftJoin('a.EstudiosTitulos', 'e')
                ->where('e = :study')
                ->setParameter('study', $study);
            $query = $qb->getQuery();
            $query->setMaxResults(5);
            $query->setFirstResult(($offset - 1) * 5);
            $students = $query->getResult();
        } else $students = $study->getAlumnos();
        return $students;
    }

    /**
     * Elimina un estudio existente de la base de datos
     * @param EstudioTitulo $study Empresa a eliminar de la BD
     */
    public function removeEstudio(EstudioTitulo $study)
    {
        $em = $this->app['orm.em'];
        $em->remove($study);
        $em->flush();
    }
}