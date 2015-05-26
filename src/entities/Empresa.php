<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="empresa")
 */
class Empresa {
    /**
     * @ORM\Id
     * @ORM\Column(length=9)
     */
    private $CIF;

    /**
     * @ORM\Column(length=20)
     */
    private $Denominacion;

    /**
     * @ORM\Column(length=50, nullable=true)
     */
    private $Direccion;

    /**
     * @ORM\Column(length=10)
     */
    private $Telefono;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $CP;

    /**
     * @ORM\Column(length=30)
     */
    private $Email;

    /**
     * @ORM\Column(length=100, nullable=true)
     */
    private $Contacto;

    /**
     * @ORM\ManyToMany(targetEntity="Alumno", mappedBy="Empresas", cascade={"all"})
     */
    private $Alumnos;

    /**
     * @ORM\ManyToMany(targetEntity="Categoria_Actividad", mappedBy="Empresas", cascade={"all"})
     */
    private $Actividades;

    public function __construct() {
        $this->Alumnos = new ArrayCollection();
        $this->Actividades = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCIF()
    {
        return $this->CIF;
    }

    /**
     * @param mixed $CIF
     */
    public function setCIF($CIF)
    {
        $this->CIF = $CIF;
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
    public function getDireccion()
    {
        return $this->Direccion;
    }

    /**
     * @param mixed $Direccion
     */
    public function setDireccion($Direccion)
    {
        $this->Direccion = $Direccion;
    }

    /**
     * @return mixed
     */
    public function getTelefono()
    {
        return $this->Telefono;
    }

    /**
     * @param mixed $Telefono
     */
    public function setTelefono($Telefono)
    {
        $this->Telefono = $Telefono;
    }

    /**
     * @return mixed
     */
    public function getCP()
    {
        return $this->CP;
    }

    /**
     * @param mixed $CP
     */
    public function setCP($CP)
    {
        $this->CP = $CP;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param mixed $Email
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     * @return mixed
     */
    public function getContacto()
    {
        return $this->Contacto;
    }

    /**
     * @param mixed $Contacto
     */
    public function setContacto($Contacto)
    {
        $this->Contacto = $Contacto;
    }
}