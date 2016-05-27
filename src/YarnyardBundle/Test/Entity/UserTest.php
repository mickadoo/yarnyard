<?php

namespace YarnyardBundle\Test\Entity;

use YarnyardBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function userWithUuidAnonymousWillBeAnonymous()
    {
        $user = new User('anonymous');

        $this->assertTrue($user->isAnonymous());
    }

    /**
     * @test
     */
    public function userWithNonAnonymousUuidWillNotBeAnonymous()
    {
        $user = new User('03l2lckl34l2locl3kl3j');

        $this->assertFalse($user->isAnonymous());
    }

    /**
     * @test
     */
    public function anonymousUserWillHaveNoRoles()
    {
        $user = new User('anonymous');

        $this->assertEmpty($user->getRoles());
    }

    /**
     * @test
     */
    public function nonAnonymousUserWillHaveUserRole()
    {
        $user = new User('aslkfj4lkjxv90883lk34');

        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
