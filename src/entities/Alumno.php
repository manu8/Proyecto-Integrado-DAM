<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="alumno")
 */
class Alumno {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $Id;

    /**
     * @Column(length=9)
     */
    private $NIF;

    /**
     * @Column(length=20)
     */
    private $Nombre;

    /**
     * @Column(length=40)
     */
    private $Apellidos;

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
     * @ManyToMany(targetEntity="EstudioTitulo", inversedBy="Alumnos", cascade={"all"})
     * @JoinTable(name="alumnos_estudios",
     *      joinColumns={@JoinColumn(name="alumno_id", referencedColumnName="Id")},
     *      inverseJoinColumns={@JoinColumn(name="estudio_id", referencedColumnName="Id", nullable=false)}
     * )
     */
    private $EstudiosTitulos;

    /**
     * @ManyToMany(targetEntity="HabilidadConocimiento", inversedBy="Alumnos", cascade={"all"})
     * @JoinTable(name="alumnos_conocimientos",
     *      joinColumns={@JoinColumn(name="alumno_id", referencedColumnName="Id", nullable=false)},
     *      inverseJoinColumns={@JoinColumn(name="conocimiento_id", referencedColumnName="Id")}
     * )
     */
    private $ConocimientosHabilidades;

    /**
     * @ManyToMany(targetEntity="Empresa", inversedBy="Alumnos", cascade={"all"})
     * @JoinTable(name="alumnos_empresas",
     *      joinColumns={@JoinColumn(name="alumno_id", referencedColumnName="Id")},
     *      inverseJoinColumns={@JoinColumn(name="empresa_id", referencedColumnName="Id", nullable=false)}
     * )
     */
    private $Empresas;

    public function __construct($nif, $name, $surnames, $address, $cp, $email) {
        $this->NIF = $nif;
        $this->Nombre = $name;
        $this->Apellidos = $surnames;
        $this->Direccion = $address;
        $this->CP = $cp;
        $this->Email = $email;
        $this->EstudiosTitulos = new ArrayCollection();
        $this->ConocimientosHabilidades = new ArrayCollection();
        $this->Empresas = new ArrayCollection();
    }

    /**
     * @return integer Id del alumno
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string NIF del alumno
     */
    public function getNIF()
    {
        return $this->NIF;
    }

    /**
     * @param string $NIF NIF del alumno
     */
    public function setNIF($NIF)
    {
        $this->NIF = $NIF;
    }

    /**
     * @return string Nombre del alumno
     */
    public function getNombre()
    {
        return $this->Nombre;
    }

    /**
     * @param string $Nombre Nombre del alumno
     */
    public function setNombre($Nombre)
    {
        $this->Nombre = $Nombre;
    }

    /**
     * @return string Apellidos del alumno
     */
    public function getApellidos()
    {
        return $this->Apellidos;
    }

    /**
     * @param string $Apellidos Apellidos del alumno
     */
    public function setApellidos($Apellidos)
    {
        $this->Apellidos = $Apellidos;
    }

    /**
     * @return string Dirección de residencia del alumno
     */
    public function getDireccion()
    {
        return $this->Direccion;
    }

    /**
     * @param string $Direccion Dirección de residencia del alumno
     */
    public function setDireccion($Direccion)
    {
        $this->Direccion = $Direccion;
    }

    /**
     * @return string Teléfono del alumno
     */
    public function getTelefono()
    {
        return $this->Telefono;
    }

    /**
     * @param string $Telefono Teléfono del alumno
     */
    public function setTelefono($Telefono)
    {
        $this->Telefono = $Telefono;
    }

    /**
     * @return integer Código Postal de la residencia del alumno
     */
    public function getCP()
    {
        return $this->CP;
    }

    /**
     * @param mixed $CP Código Postal de la residencia del alumno
     */
    public function setCP($CP)
    {
        $this->CP = $CP;
    }

    /**
     * @return string Email del alumno
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param string $Email Email del alumno
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     * @return array Estudios cursados del alumno
     */
    public function getEstudiosTitulos()
    {
        return $this->EstudiosTitulos;
    }

    /**
     * @param EstudioTitulo $study Estudio a añadir
     */
    public function addStudy(EstudioTitulo $study)
    {
        $studies = $this->EstudiosTitulos;
        if(!$studies->contains($study)) $studies->add($study);
    }

    /**
     * @param EstudioTitulo $study Estudio a eliminar
     */
    public function removeStudy(EstudioTitulo $study)
    {
        $studies = $this->EstudiosTitulos;
        if($studies->contains($study)) $studies->removeElement($study);
    }

    /**
     * @return array Conocimientos o habilidades adicionales del alumno
     */
    public function getConocimientosHabilidades()
    {
        return $this->ConocimientosHabilidades;
    }

    /**
     * @param HabilidadConocimiento $knowledge Conocimiento a añadir
     */
    public function addKnowledge(HabilidadConocimiento $knowledge)
    {
        $knowledgeList = $this->ConocimientosHabilidades;
        if(!$knowledgeList->contains($knowledge)) $knowledgeList->add($knowledge);
    }

    /**
     * @param HabilidadConocimiento $knowledge Conocimiento a eliminar
     */
    public function removeKnowledge(HabilidadConocimiento $knowledge)
    {
        $knowledgeList = $this->ConocimientosHabilidades;
        if($knowledgeList->contains($knowledge)) $knowledgeList->removeElement($knowledge);
    }

    /**
     * @return array Empresas en las que ha realizado algún trabajo el alumno
     */
    public function getEmpresas()
    {
        return $this->Empresas;
    }

    /**
     * @param Empresa $company Empresa a añadir
     */
    public function addCompany(Empresa $company)
    {
        $companies = $this->Empresas;
        if(!$companies->contains($company)) $companies->add($company);
    }

    /**
     * @param Empresa $company Empresa a eliminar
     */
    public function removeCompany(Empresa $company)
    {
        $companies = $this->Empresas;
        if($companies->contains($company)) $companies->removeElement($company);
    }
}