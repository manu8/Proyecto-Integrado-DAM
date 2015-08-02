<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
* @Entity
* @Table(name="estudio_titulo")
*/
class EstudioTitulo {
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $Id;

    /**
     * @Column(length=50)
     */
    private $Denominacion;

    /**
     * @Column(type="text", nullable=true)
     */
    private $Descripcion;

    /**
     * @ManyToMany(targetEntity="Alumno", mappedBy="EstudiosTitulos", cascade={"persist"})
     */
    private $Alumnos;

    /***
     * @ManyToMany(targetEntity="CategoriaActividad", mappedBy="EstudiosTitulos", cascade={"persist"})
     */
    private $Categorias;

    public function __construct() {
        $this->Alumnos = new ArrayCollection();
        $this->Categorias = new ArrayCollection();
    }

    /**
     * @return integer Id del estudio
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string Nombre del estudio
     */
    public function getDenominacion()
    {
        return $this->Denominacion;
    }

    /**
     * @param string $Denominacion Nombre del estudio
     */
    public function setDenominacion($Denominacion)
    {
        $this->Denominacion = $Denominacion;
    }

    /**
     * @return string Descripción breve del estudio
     */
    public function getDescripcion()
    {
        return $this->Descripcion;
    }

    /**
     * @param string $Descripcion Descripción breve del estudio
     */
    public function setDescripcion($Descripcion)
    {
        $this->Descripcion = $Descripcion;
    }

    /**
     * @return array Alumnos poseedores de este estudio
     */
    public function getAlumnos()
    {
        return $this->Alumnos;
    }

    /**
     * @param Alumno $student Alumno a añadir
     */
    public function addAlumno(Alumno $student)
    {
        $students = $this->Alumnos;
        if(!$students->contains($student)) $students->add($student);
    }

    /**
     * @param Alumno $student Alumno a eliminar
     */
    public function removeAlumno(Alumno $student)
    {
        $students = $this->Alumnos;
        if($students->contains($student)) $students->removeElement($student);
    }

    /**
     * @return array Categorías a las que pertenece el estudio
     */
    public function getCategorias()
    {
        return $this->Categorias;
    }

    /**
     * @param CategoriaActividad $category Categoría a añadir
     */
    public function addCategoria(CategoriaActividad $category)
    {
        $categories = $this->Categorias;
        if(!$categories->contains($category)) $categories->add($category);
    }

    /**
     * @param CategoriaActividad $category Categoría a eliminar
     */
    public function removeCategoria(CategoriaActividad $category)
    {
        $categories = $this->Categorias;
        if($categories->contains($category)) $categories->removeElement($category);
    }
}