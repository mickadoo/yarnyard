<?php

namespace YarnyardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use YarnyardBundle\Exception\Constants\UserFields;

class User implements UserFields, UserInterface
{
    /**
     * @Groups({"user"})
     *
     * @var integer
     */
    private $id;

    /**
     * @Groups({"user"})
     *
     * @var string
     */
    private $uuid;

    /**
     * @Groups({"user"})
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     *
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
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
    public function getUuid()
    {
        return $this->uuid;
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
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        // needed by interface
    }
}
