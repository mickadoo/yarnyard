<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class UserRepository extends EntityRepository
{

    /**
     * @param User $user
     * @return User
     */
    public function save(User $user)
    {
        $encoder = new BCryptPasswordEncoder(15);
        $user->setSalt(uniqid(mt_rand(), true));
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());

        $user->setPassword($password);

        $this->_em->persist($user);
        $this->_em->flush($user);

        return $user;
    }

}