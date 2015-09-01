<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="habilidad_conocimiento")
 */
class HabilidadConocimiento {
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
     * @ManyToMany(targetEntity="Alumno", mappedBy="ConocimientosHabilidades", cascade={"persist"})
     */
    private $Alumnos;

    /**
     * @ManyToMany(targetEntity="CategoriaActividad", inversedBy="HabilidadesConocimientos", cascade={"persist"})
     * @JoinTable(name="conocimientos_categorias",
     *      joinColumns={@JoinColumn(name="conocimiento_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@JoinColumn(name="categoria_id", referencedColumnName="Id")}
     * )
     */
    private $Categorias;

    public function __construct() {
        $this->Alumnos = new ArrayCollection();
        $this->Categorias = new ArrayCollection();
    }

    /**
     * @return integer Id del conocimiento o habilidad
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string Nombre del conocimiento o habilidad
     */
    public function getDenominacion()
    {
        return $this->Denominacion;
    }

    /**
     * @param string $Denominacion Nombre del conocimiento o habilidad
     */
    public function setDenominacion($Denominacion)
    {
        $this->Denominacion = $Denominacion;
    }

    /**
     * @return string Descripción del conocimiento o habilidad
     */
    public function getDescripcion()
    {
        return $this->Descripcion;
    }

    /**
     * @param string $Descripcion Descripción del conocimiento o habilidad
     */
    public function setDescripcion($Descripcion)
    {
        $this->Descripcion = $Descripcion;
    }

    /**
     * @return array Alumnos que poseen este conocimiento o habilidad
     */
    public function getAlumnos()
    {
        return $this->Alumnos;
    }

    /**
     * @return array Categorías a las que pertenece este conocimiento o habilidad
     */
    public function getCategorias()
    {
        return $this->Categorias;
    }

    /**
     * @param CategoriaActividad $category Categoría a añadir
     */
    public function addCategory(CategoriaActividad $category)
    {
        $categories = $this->Categorias;
        if(!$categories->contains($category)) $categories->add($category);
    }

    /**
     * @param CategoriaActividad $category Categoría a eliminar
     */
    public function removeCategory(CategoriaActividad $category)
    {
        $categories = $this->Categorias;
        if($categories->contains($category)) $categories->removeElement($category);
    }
}