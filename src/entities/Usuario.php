<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="usuario")
 */
class Usuario implements UserInterface {

    /**
     * @ORM\Column(type="integer")
     */
    private $Id;

    /**
     * @ORM\Id
     * @ORM\Column(length=30)
     */
    private $Username;

    /**
     * @ORM\Column
     */
    private $Password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Activo;

    private $salt;

    private $rol = 'ROLE_USER';

    public function __construct($username, $passwd) {
        $this->Id = uniqid(mt_rand(), true);
        $this->Username = $username;
        $this->Password = $passwd;
        $this->Activo = false;
        $this->salt = base_convert(sha1($this->Id), 16, 36);
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
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * @param mixed $Username
     */
    public function setUsername($username)
    {
        $this->Username = $username;
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

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->rol;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->getRol() === $role;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function eraseCredentials() {}
}