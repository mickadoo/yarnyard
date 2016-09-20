<?php

namespace YarnyardBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class ParticipationGrant
{
    /**
     * @var int
     *
     * @Groups({"participationGrant"})
     */
    private $id;

    /**
     * @var Story
     */
    private $story;

    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $createdBy;

    /**
     * @Groups({"participationGrant"})
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Story
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * @param Story $story
     *
     * @return $this
     */
    public function setStory($story)
    {
        $this->story = $story;

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
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
