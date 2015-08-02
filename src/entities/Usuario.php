<?php

namespace Entities;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Entity
 * @Table(name="usuario")
 */
class Usuario implements UserInterface {

    /**
     * @Column(type="integer")
     */
    private $Id;

    /**
     * @Id
     * @Column(length=30)
     */
    private $Username;

    /**
     * @Column
     */
    private $Password;

    /**
     * @Column(type="boolean")
     */
    private $Activo;

    /**
     * @Column(length=10)
     */
    private $Rol;

    private $roles = array('ROLE_USER');

    private $salt;

    public function __construct($username, $passwd) {
        $this->Id = uniqid(mt_rand(), true);
        $this->Username = $username;
        $this->Password = $passwd;
        $this->Activo = false;
        $this->Rol = $this->getRoles()[0];
    }

    /**
     * @return integer Id del usuario
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string Nombre de usuario
     */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * @param string $Username Nombre de usuario
     */
    public function setUsername($username)
    {
        $this->Username = $username;
    }

    /**
     * @return string Contraseña del usuario
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * @param string $Password Contraseña del usuario
     */
    public function setPassword($Password)
    {
        $this->Password = $Password;
    }

    /**
     * @return boolean Usuario activo
     */
    public function getActivo()
    {
        return $this->Activo;
    }

    /**
     * @param boolean $Activo Usuario activo
     */
    public function setActivo($Activo)
    {
        $this->Activo = $Activo;
    }

    /**
     * @return array Roles del usuario
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $role Rol del usuario
     * @return bool True si posee el rol false si no
     */
    public function hasRole($role)
    {
        return $this->getRoles() === $role;
    }

    /**
     * @return string Codificador de la contraseña
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Elimina las credenciales del usuario
     */
    public function eraseCredentials() {}
}