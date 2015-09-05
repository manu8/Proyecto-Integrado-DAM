<?php

namespace Lib\Providers;


use Entities\CategoriaActividad;

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
    public function getCategorias($offset = null)
    {
        $categories = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!is_null($offset)){
                $query = $em->createQuery('SELECT c FROM Entities:CategoriaActividad c');
                $query->setMaxResults(5);
                $query->setFirstResult(($offset - 1) * 5);
                $categories = $query->getResult();
            }
            else $categories = $em->getRepository('Entities\CategoriaActividad')->findAll();
        }
        return $categories;
    }

    public function getCategoria($id)
    {
        $category = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            $category = $em->getRepository('Entities\CategoriaActividad')->findOneBy(array('Id' => $id));
        }
        return $category;
    }

    public function getCompanies(CategoriaActividad $category, $offset = null)
    {
        $em = $this->app['orm.em'];
        if(!is_null($offset)){
            $qb = $em->createQueryBuilder();
            $qb->select('e')
                ->from('Entities:Empresa', 'e')
                ->leftJoin('e.Actividades', 'a')
                ->where('a = :category')
                ->setParameter('category', $category);
            $query = $qb->getQuery();
            $query->setMaxResults(5);
            $query->setFirstResult(($offset - 1) * 5);
            $companies = $query->getResult();
        } else $companies = $category->getEmpresas();
        return $companies;
    }

    public function getStudies(CategoriaActividad $category, $offset = null)
    {
        $em = $this->app['orm.em'];
        if(!is_null($offset)){
            $qb = $em->createQueryBuilder();
            $qb->select('t')
                ->from('Entities:EstudioTitulo', 't')
                ->leftJoin('t.Categorias', 'c')
                ->where('c = :category')
                ->setParameter('category', $category);
            $query = $qb->getQuery();
            $query->setMaxResults(5);
            $query->setFirstResult(($offset - 1) * 5);
            $studies = $query->getResult();
        } else $studies = $category->getEstudiosTitulos();
        return $studies;
    }

    public function getKnowledges(CategoriaActividad $category, $offset = null)
    {
        $em = $this->app['orm.em'];
        if(!is_null($offset)){
            $qb = $em->createQueryBuilder();
            $qb->select('k')
                ->from('Entities:HabilidadConocimiento', 'k')
                ->leftJoin('k.Categorias', 'c')
                ->where('c = :category')
                ->setParameter('category', $category);
            $query = $qb->getQuery();
            $query->setMaxResults(5);
            $query->setFirstResult(($offset - 1) * 5);
            $studies = $query->getResult();
        } else $studies = $category->getHabilidadesConocimientos();
        return $studies;
    }

    /**
     * Crea una nueva categoría en la base de datos
     * @param CategoriaActividad $category Categoría/Actividad a almacenar en la BD
     */
    public function createCategoria(CategoriaActividad $category)
    {
        $em = $this->app['orm.em'];
        $em->persist($category);
        $em->flush();
    }

    /**
     * Elimina una categoría existente de la base de datos
     * @param CategoriaActividad $category Categoría a eliminar de la BD
     */
    public function removeCategoria(CategoriaActividad $category)
    {
        $em = $this->app['orm.em'];
        $em->remove($category);
        $em->flush();
    }
}