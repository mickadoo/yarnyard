<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\Roles;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\Role;
use Mickadoo\Yarnyard\Library\EntityHelper\FixtureReference;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
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

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return FixtureReference::ORDER_ROLE;
    }
}
