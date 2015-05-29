<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="habilidad_conocimiento")
 */
class Habilidad_Conocimiento {
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
     * @ORM\ManyToMany(targetEntity="Alumno", mappedBy="Conocimientos_Habilidades", cascade={"all"})
     */
    private $Alumnos;

    /**
     * @ORM\ManyToMany(targetEntity="Categoria_Actividad", mappedBy="Habilidades_Conocimientos", cascade={"all"})
     */
    private $Categorias;

    public function __construct() {
        $this->Alumnos = new ArrayCollection();
        $this->Categorias = new ArrayCollection();
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