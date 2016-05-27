<?php

namespace YarnyardBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class User implements UserInterface
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
     * @var string
     */
    private $username;

    /**
     * @Groups({"user"})
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Groups({"user"})
     *
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
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
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