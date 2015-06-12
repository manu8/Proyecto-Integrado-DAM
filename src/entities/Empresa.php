<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="empresa")
 */
class Empresa {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $Id;

    /**
     * @Column(length=9)
     */
    private $CIF;

    /**
     * @Column(length=20)
     */
    private $Denominacion;

    /**
     * @Column(length=50, nullable=true)
     */
    private $Direccion;

    /**
     * @Column(length=10)
     */
    private $Telefono;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $CP;

    /**
     * @Column(length=30)
     */
    private $Email;

    /**
     * @Column(length=100, nullable=true)
     */
    private $Contacto;

    /**
     * @ManyToMany(targetEntity="Alumno", mappedBy="Empresas", cascade={"all"})
     */
    private $Alumnos;

    /**
     * @ManyToMany(targetEntity="CategoriaActividad", mappedBy="Empresas", cascade={"all"})
     */
    private $Actividades;

    public function __construct() {
        $this->Alumnos = new ArrayCollection();
        $this->Actividades = new ArrayCollection();
    }

    /**
     * @return integer Id de la empresa
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string CIF de la empresa
     */
    public function getCIF()
    {
        return $this->CIF;
    }

    /**
     * @param string $CIF CIF de la empresa
     */
    public function setCIF($CIF)
    {
        $this->CIF = $CIF;
    }

    /**
     * @return string Nombre de la empresa
     */
    public function getDenominacion()
    {
        return $this->Denominacion;
    }

    /**
     * @param string $Denominacion Nombre de la empresa
     */
    public function setDenominacion($Denominacion)
    {
        $this->Denominacion = $Denominacion;
    }

    /**
     * @return string Dirección de la empresa
     */
    public function getDireccion()
    {
        return $this->Direccion;
    }

    /**
     * @param string $Direccion Dirección de la empresa
     */
    public function setDireccion($Direccion)
    {
        $this->Direccion = $Direccion;
    }

    /**
     * @return string Teléfono de la empresa
     */
    public function getTelefono()
    {
        return $this->Telefono;
    }

    /**
     * @param string $Telefono Teléfono de la empresa
     */
    public function setTelefono($Telefono)
    {
        $this->Telefono = $Telefono;
    }

    /**
     * @return integer Código Postal de la dirección de la empresa
     */
    public function getCP()
    {
        return $this->CP;
    }

    /**
     * @param integer $CP Código Postal de la dirección de la empresa
     */
    public function setCP($CP)
    {
        $this->CP = $CP;
    }

    /**
     * @return string Email de la empresa
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param string $Email Email de la empresa
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     * @return string Nombre completo de la persona de contacto en la empresa
     */
    public function getContacto()
    {
        return $this->Contacto;
    }

    /**
     * @param string $Contacto Nombre completo de la persona de contacto en la empresa
     */
    public function setContacto($Contacto)
    {
        $this->Contacto = $Contacto;
    }

    /**
     * @return array Alumnos que han trabajado en la empresa
     */
    public function getAlumnos()
    {
        return $this->Alumnos;
    }

    /**
     * @param Alumno $student Alumno a añadir
     */
    public function addStudent(Alumno $student)
    {
        $students = $this->Alumnos;
        if(!$students->contains($student)) $students->add($student);
    }

    /**
     * @param Alumno $student Alumno a eliminar
     */
    public function removeStudent(Alumno $student)
    {
        $students = $this->Alumnos;
        if($students->contains($student)) $students->removeElement($student);
    }

    /**
     * @return array Actividades (Categorías) a las que pertenece la empresa
     */
    public function getActividades()
    {
        return $this->Actividades;
    }

    /**
     * @param CategoriaActividad $activity Categoría a añadir
     */
    public function addActivity(CategoriaActividad $activity)
    {
        $activities = $this->Actividades;
        if(!$activities->contains($activity)) $activities->add($activity);
    }

    /**
     * @param CategoriaActividad $activity Categoría a eliminar
     */
    public function removeActivity(CategoriaActividad $activity)
    {
        $activities = $this->Actividades;
        if($activities->contains($activity)) $activities->removeElement($activity);
    }
}