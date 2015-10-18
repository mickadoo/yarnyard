<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Entity;

use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Symfony\Component\Security\Core\Role\RoleInterface;

class Role implements RoleInterface
{
    const ACTIVE_USER = 1;

    /**
     * @var array
     */
    private $validRoles = [
        self::ACTIVE_USER
    ];

    /**
     * @var string
     */
    private $role;

    /**
     * @var User
     */
    private $user;

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param $role
     * @return $this
     * @throws YarnyardException
     */
    public function setRole($role)
    {
        if (!in_array($role, $this->validRoles)) {
            throw new YarnyardException('ERR_INVALID_ROLE');
        }

        $this->role = $role;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
