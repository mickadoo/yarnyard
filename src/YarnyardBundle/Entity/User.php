<?php

namespace YarnyardBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use YarnyardBundle\Exception\Constants\UserFields;

class User implements UserFields, UserInterface
{
    /**
     * @Groups({"user"})
     *
     * @var int
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
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @param string $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return int
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
     * @return array
     */
    public function getRoles()
    {
        return [];
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
