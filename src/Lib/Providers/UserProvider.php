<?php

namespace Lib\Providers;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Entities\Usuario;

class UserProvider implements UserProviderInterface {

    private $app;

    public function __construct(\Silex\Application $app) {
        $this->app = $app;
    }

    public function loadUserByUsername($username) {
        $user = null;
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!$user = $em->getRepository('Entities\Usuario')->findOneBy(array('Username' => $username))){
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }
        }
        return $user;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof Usuario) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === '\Entities\Usuario';
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

    public function checkUserPassword(Usuario $user)
    {
        return $user->getPassword() === $this->encodeUserPassword($user);
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

    public function createUser(Usuario $user)
    {
        $em = $this->app['orm.em'];
        if(is_null($em->getRepository('Entities\Usuario')->find($user->getUsername()))){
            $this->setUserPassword($user);
            $em->persist($user);
            $em->flush();
            return true;
        } else return false;
    }

    public function activateUser($id)
    {
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!$user = $em->getRepository("Usuario")->findOneBy(array("Id"=>$id))){
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $id));
            } else {
                $user->setActivo(true);
                $em->persist($user);
                $em->flush();
            }
        }
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
}