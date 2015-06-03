<?php

namespace Lib\Providers;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface {

    private $app;

    public function __construct(\Silex\Application $app) {
        $this->app = $app;
    }

    public function loadUserByUsername($username) {
        $user = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!$user = $em->getRepository('Entities\Usuario')->findOneBy(array("Username"=>$username))){
                var_dump($user);
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }
        }
        return $user;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof \Entities\Usuario) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getEmail());
    }

    public function supportsClass($class) {
        return $class === '\Entities\Usuario';
    }

    public function getEncoder(UserInterface $user)
    {
        return $this->app['security.encoder_factory']->getEncoder($user);
    }

    public function encodeUserPassword(UserInterface $user)
    {
        $encoder = $this->getEncoder($user);
        return $encoder->encodePassword($user->getPassword(), $user->getSalt());
    }

    public function setUserPassword(UserInterface $user)
    {
        $user->setPassword($this->encodeUserPassword($user));
    }

    public function checkUserPassword(UserInterface $user)
    {
        return $user->getPassword() === $this->encodeUserPassword($user);
    }

    function isLoggedIn()
    {
        $token = $this->app['security']->getToken();
        if (null === $token) {
            return false;
        }
        return $this->app['security']->isGranted('IS_AUTHENTICATED_FULLY');
    }

    function getCurrentUser() {
        if($this->isLoggedIn()){
            return $token = $this->app['security']->getToken()->getUser();
        }
        return null;
    }

    public function createUser(UserInterface $user) {
        $em = $this->app['orm.em'];
        $this->setUserPassword($user);
        $em->persist($user);
        $em->flush();
    }

    public function activateUser($id)
    {
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!$user = $em->getRepository("Entities\\Usuario")->findOneBy(array("Id"=>$id))){
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $id));
            } else {
                $user->setActivo(true);
                $em->persist($user);
                $em->flush();
            }
        }
    }

    public function updateUser(UserInterface $user)
    {
        $em = $this->app['orm.em'];
        $em->persist($user);
        $em->flush();
    }

    public function deleteUser(UserInterface $user)
    {
        $em = $this->app['orm.em'];
        $em->remove($user);
        $em->flush();
    }
}