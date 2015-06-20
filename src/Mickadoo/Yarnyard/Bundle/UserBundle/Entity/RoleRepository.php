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
        $role = $this->findOneBy(['role' => $roleName]);

        if (! $role) {
            throw new YarnyardException('ROLE_NOT_EXISTS');
        }

        $userRole = new UserRole();
        $userRole
            ->setUser($user)
            ->setRole($role);

        $this->_em->persist($userRole);
        $this->_em->flush($userRole);

        return $user;
    }
}
