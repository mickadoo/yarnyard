<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\Roles;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\Role;

class LoadRoleData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roles = [Roles::ACTIVE_USER];

        foreach ($roles as $roleName) {
            $role = new Role();
            $role->setRole($roleName);

            $manager->persist($role);
            $manager->flush($role);
        }
    }
}
