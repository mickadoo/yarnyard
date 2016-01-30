<?php

namespace YarnyardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use YarnyardBundle\Constants\UserFields;
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
    private $uuid;

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
     * User constructor.
     * @param string $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->uuid;
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
     * @Serializer(ignorable=true)
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @Serializer(ignorable=true)
     * @return string
     */
    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        // wat
    }
}
