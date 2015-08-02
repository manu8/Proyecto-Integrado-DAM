<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="categoria_actividad")
 */
class CategoriaActividad {

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
     * @ManyToMany(targetEntity="EstudioTitulo", inversedBy="Categorias", cascade={"persist"})
     * @JoinTable(name="estudios_categorias",
     *      joinColumns={@JoinColumn(name="categoria_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@JoinColumn(name="estudio_id", referencedColumnName="Id", nullable=false)}
     * )
     */
    private $EstudiosTitulos;

    /**
     * @ManyToMany(targetEntity="HabilidadConocimiento", inversedBy="Categorias", cascade={"persist"})
     * @JoinTable(name="conocimientos_categorias",
     *      joinColumns={@JoinColumn(name="categoria_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@JoinColumn(name="conocimiento_id", referencedColumnName="Id")}
     * )
     */
    private $HabilidadesConocimientos;

    /**
     * @ManyToMany(targetEntity="Empresa", mappedBy="Actividades", cascade={"persist"})
     */
    private $Empresas;

    public function __construct() {
        $this->Estudios_Titulos = new ArrayCollection();
        $this->Habilidades_Conocimientos = new ArrayCollection();
        $this->Empresas = new ArrayCollection();
    }

    /**
     * @return integer Id de la categoría
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string Nombre de la categoría
     */
    public function getDenominacion()
    {
        return $this->Denominacion;
    }

    /**
     * @param string $Denominacion Nombre de la categoría
     */
    public function setDenominacion($Denominacion)
    {
        $this->Denominacion = $Denominacion;
    }

    /**
     * @return string Descripción de la categoría
     */
    public function getDescripcion()
    {
        return $this->Descripcion;
    }

    /**
     * @param string $Descripcion Descripción de la categoría
     */
    public function setDescripcion($Descripcion)
    {
        $this->Descripcion = $Descripcion;
    }

    /**
     * @return array Estudios pertenecientes a esta categoría
     */
    public function getEstudiosTitulos()
    {
        return $this->EstudiosTitulos;
    }

    /**
     * @return array Habilidades y conocimientos pertenecientes a esta categoría
     */
    public function getHabilidadesConocimientos()
    {
        return $this->HabilidadesConocimientos;
    }

    /**
     * @return array Empresas pertenecientes a esta categoría
     */
    public function getEmpresas()
    {
        return $this->Empresas;
    }
}