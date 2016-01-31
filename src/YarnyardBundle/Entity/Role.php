<?php

namespace YarnyardBundle\Entity;

use YarnyardBundle\Exception\Constants\Errors;
use YarnyardBundle\Exception\YarnyardException;
use Symfony\Component\Security\Core\Role\RoleInterface;

class Role implements RoleInterface
{
    const ADMIN = 1;

    /**
     * @var array
     */
    private $validRoles = [
        self::ADMIN
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
            throw new YarnyardException(Errors::ERROR_ROLE_INVALID);
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
