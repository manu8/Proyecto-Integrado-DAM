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

    public function createUser($user) {
        $em = $this->app['orm.em'];
        $em->persist($user);
        $em->flush();
    }

    public function activateUser($id) {
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

    public function loadUserByUsername($email) {
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){
            if(!$user = $em->getRepository("Entities\\Usuario")->findOneBy(array("Email"=>$email))){
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $email));
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
}