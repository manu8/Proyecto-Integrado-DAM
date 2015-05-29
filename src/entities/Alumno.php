<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="alumno")
 */
class Alumno {
    /**
     * @ORM\Id
     * @ORM\Column(length=9)
     */
    private $NIF;

    /**
     * @ORM\Column(length=20)
     */
    private $Nombre;

    /**
     * @ORM\Column(length=40)
     */
    private $Apellidos;

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
     * @ORM\ManyToMany(targetEntity="Estudio_Titulo", inversedBy="Alumnos", cascade={"all"})
     * @ORM\JoinTable(name="alumnos_estudios",
     *      joinColumns={@ORM\JoinColumn(name="alumno_nif", referencedColumnName="NIF")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="estudio_id", referencedColumnName="Id", nullable=false)}
     * )
     */
    private $Estudios_Titulos;

    /**
     * @ORM\ManyToMany(targetEntity="Habilidad_Conocimiento", inversedBy="Alumnos", cascade={"all"})
     * @ORM\JoinTable(name="alumnos_conocimientos",
     *      joinColumns={@ORM\JoinColumn(name="alumno_nif", referencedColumnName="NIF", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="conocimiento_id", referencedColumnName="Id")}
     * )
     */
    private $Conocimientos_Habilidades;

    /**
     * @ORM\ManyToMany(targetEntity="Empresa", inversedBy="Alumnos", cascade={"all"})
     * @ORM\JoinTable(name="alumnos_empresas",
     *      joinColumns={@ORM\JoinColumn(name="alumno_nif", referencedColumnName="NIF")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="empresa_cif", referencedColumnName="CIF", nullable=false)}
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
    public function getNIF()
    {
        return $this->NIF;
    }

    /**
     * @param mixed $NIF
     */
    public function setNIF($NIF)
    {
        $this->NIF = $NIF;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->Nombre;
    }

    /**
     * @param mixed $Nombre
     */
    public function setNombre($Nombre)
    {
        $this->Nombre = $Nombre;
    }

    /**
     * @return mixed
     */
    public function getApellidos()
    {
        return $this->Apellidos;
    }

    /**
     * @param mixed $Apellidos
     */
    public function setApellidos($Apellidos)
    {
        $this->Apellidos = $Apellidos;
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
}