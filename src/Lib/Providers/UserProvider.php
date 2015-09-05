<?php

namespace Lib\Providers;

use Silex\Application;
use Entities\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface {

    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function loadUserByUsername($username) {
        $em = $this->app['orm.em'];
        if (strpos($username, '@')) {
            $user = $em->getRepository('Entities\Usuario')->findOneBy(array('email' => $username));
            if (!$user) {
                throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
            }

            return $user;
        }

        $user = $em->getRepository('Entities\Usuario')->findOneBy(array('username' => $username));
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof Usuario) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Entities\Usuario';
    }

    public function getUser($id)
    {
        return $this->app['orm.em']->getRepository('Entities\Usuario')->find($id);
    }

    public function createUser($email, $plainPassword, $name = null, $roles = array())
    {
        $user = new Usuario($email);

        if (!empty($plainPassword)) {
            $this->setUserPassword($user, $plainPassword);
        }

        if ($name !== null) {
            $user->setName($name);
        }
        if (!empty($roles)) {
            $user->setRoles($roles);
        }

        return $user;
    }

    public function getEncoder(Usuario $user)
    {
        return $this->app['security.encoder_factory']->getEncoder($user);
    }

    public function encodeUserPassword(Usuario $user)
    {
        $encoder = $this->getEncoder($user);
        return $encoder->encodePassword($user->getPassword(), $user->getSalt());
    }

    public function setUserPassword(Usuario $user)
    {
        $user->setPassword($this->encodeUserPassword($user));
    }

    function isLoggedIn()
    {
        $token = $this->app['security.token_storage']->getToken();
        if (is_null($token)) {
            return false;
        }
        return $this->app['security.authorization_checker']->isGranted('ROLE_USER');
    }

    function getCurrentUser()
    {
        if($this->isLoggedIn()){
            return $this->app['security.token_storage']->getToken()->getUser();
        }
        return null;
    }

    public function validate(Usuario $user)
    {
        $em = $this->app['orm.em'];

        // Ensure email address is unique.
        $duplicates = $em->getRepository('Entities\Usuario')->findBy(array('username' => $user->getUsername()));
        if (!empty($duplicates)) {
            foreach ($duplicates as $dup) {
                if ($dup->getId() == $user->getId()) continue;
                return false;
            }
        }

        return true;
    }

    public function persistUser(Usuario $user)
    {
        $em = $this->app['orm.em'];
        $this->setUserPassword($user, $user->getPassword());
        $em->persist($user);
        $em->flush();
    }

    public function updateUser(Usuario $user)
    {
        $em = $this->app['orm.em'];
        $em->persist($user);
        $em->flush();
    }

    public function deleteUser(Usuario $user)
    {
        $em = $this->app['orm.em'];
        $em->remove($user);
        $em->flush();
    }

    public function loginUser(Usuario $user)
    {
        if (null !== ($currentToken = $this->app['security']->getToken())) {
            $providerKey = method_exists($currentToken, 'getProviderKey') ? $currentToken->getProviderKey() : $currentToken->getKey();
            $token = new UsernamePasswordToken($user, null, $providerKey);
            $this->app['security']->setToken($token);

            $this->app['user'] = $user;
        }
    }
}