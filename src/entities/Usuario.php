<?php

namespace Entities;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Entity
 * @Table(name="usuario")
 */
class Usuario implements UserInterface {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column
     */
    protected $email;

    /**
     * @Column
     */
    protected $password;

    /**
     * @Column
     */
    protected $salt;

    /**
     * @Column(type="simple_array", nullable=true)
     */
    protected $roles = array('ROLE_USER');

    /**
     * @Column(length=100)
     */
    protected $username;

    /**
     * @Column(type="boolean")
     */
    protected $isEnabled = true;

    /**
     * @Column(length=100, nullable=true)
     */
    protected $confirmationToken;

    public function __construct($email)
    {
        $this->email = $email;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->confirmationToken = self::generateToken();
    }

    /**
     * @return integer User id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string User email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email User email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string User password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password User password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string User salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt User salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return array User roles
     */
    public function getRoles()
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    /**
     * @param array $roles User roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * @return string User username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username User username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return boolean User is enabled
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param boolean $isEnabled User activate
     */
    public function setEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return string User confirmation token
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken User confirmation token
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');
    }

    private function getRandomNumber()
    {
        $nbBytes = 32;

        // try to use OpenSSL
        if (defined('PHP_WINDOWS_VERSION_BUILD') && version_compare(PHP_VERSION, '5.3.4', '>') &&
            function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);

            if (false !== $bytes && true === $strong) {
                return $bytes;
            }
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {}
}