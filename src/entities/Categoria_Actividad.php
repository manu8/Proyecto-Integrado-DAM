<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="categoria_actividad")
 */
class Categoria_Actividad {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;

    /**
     * @ORM\Column(length=50)
     */
    private $Denominacion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Descripcion;

    /**
     * @ORM\ManyToMany(targetEntity="Estudio_Titulo", inversedBy="Categorias", cascade={"all"})
     * @ORM\JoinTable(name="estudios_categorias",
     *      joinColumns={@ORM\JoinColumn(name="categoria_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="estudio_id", referencedColumnName="Id", nullable=false)}
     * )
     */
    private $Estudios_Titulos;

    /**
     * @ORM\ManyToMany(targetEntity="Habilidad_Conocimiento", inversedBy="Categoria", cascade={"all"})
     * @ORM\JoinTable(name="conocimientos_categorias",
     *      joinColumns={@ORM\JoinColumn(name="categoria_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="conocimiento_id", referencedColumnName="Id")}
     * )
     */
    private $Conocimientos_Habilidades;

    /**
     * @ORM\ManyToMany(targetEntity="Empresa", inversedBy="Actividades", cascade={"all"})
     * @ORM\JoinTable(name="empresas_actividades",
     *      joinColumns={@ORM\JoinColumn(name="categoria_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="empresa_cif", referencedColumnName="CIF")}
     * )
     */
    private $Empresas;

    public function __construct() {
        $this->Estudios_Titulos = new ArrayCollection();
        $this->Conocimientos_Habilidades = new ArrayCollection();
        $this->Empresas = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return mixed
     */
    public function getDenominacion()
    {
        return $this->Denominacion;
    }

    /**
     * @param mixed $Denominacion
     */
    public function setDenominacion($Denominacion)
    {
        $this->Denominacion = $Denominacion;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->Descripcion;
    }

    /**
     * @param mixed $Descripcion
     */
    public function setDescripcion($Descripcion)
    {
        $this->Descripcion = $Descripcion;
    }
}