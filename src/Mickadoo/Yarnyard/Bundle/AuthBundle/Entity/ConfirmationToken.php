<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationTokenRepository")
 */
class ConfirmationToken
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255 )
     */
    protected $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime",nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime",nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $expiresAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

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
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     * @return $this
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
