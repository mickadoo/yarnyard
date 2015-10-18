<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserFields;
use Mickadoo\Yarnyard\Library\Annotation\Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserFields, UserInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     *
     * @Serializer(ignorable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @Serializer(ignorable=true)
     */
    private $salt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var ArrayCollection|Role[]
     */
    private $roles;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        if ($this->roles) {
            return $this->roles->toArray();
        }

        return [];
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function addRole(Role $role)
    {
        if ($this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param $salt
     *
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

}
