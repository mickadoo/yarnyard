<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;

class RoleRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param string $roleName
     * @return User
     * @throws YarnyardException
     */
    public function addRoleForUser(User $user, $roleName)
    {
        $role = new Role();
        $role->setRole($roleName)
            ->setUser($user);

        $user->addRole($role);

        $this->_em->flush($user);

        return $user;
    }
}
