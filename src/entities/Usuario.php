<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="usuario")
 */
class Usuario {
    /**
     * @ORM\Id
     * @ORM\Column(length=30)
     */
    private $Email;

    /**
     * @ORM\Column(length=20)
     */
    private $Password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Activo;

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
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * @param mixed $Password
     */
    public function setPassword($Password)
    {
        $this->Password = $Password;
    }

    /**
     * @return mixed
     */
    public function getActivo()
    {
        return $this->Activo;
    }

    /**
     * @param mixed $Activo
     */
    public function setActivo($Activo)
    {
        $this->Activo = $Activo;
    }
}