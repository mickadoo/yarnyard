<?php

namespace YarnyardBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Story
{
    /**
     * @Groups({"story", "elastica"})
     *
     * @var int
     */
    private $id;

    /**
     * @Groups({"story", "elastica"})
     *
     * @var string
     */
    private $title;

    /**
     * @Groups({"story"})
     *
     * @var bool
     */
    private $completed = false;

    /**
     * If true contributors are selected at random. If false owner must invite.
     *
     * @Groups({"story"})
     *
     * @var bool
     */
    private $random;

    /**
     * Number of sentences each contributor can add before story is completed.
     *
     * @Groups({"story"})
     *
     * @var int
     */
    private $rounds = 0;

    /**
     * @Groups({"story"})
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Groups({"story"})
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var User
     */
    private $createdBy;

    /**
     * @var User
     */
    private $updatedBy;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Story
     */
    public function setTitle(string $title) : Story
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     *
     * @return Story
     */
    public function setCompleted(bool $completed) : Story
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRandom()
    {
        return $this->random;
    }

    /**
     * @param bool $random
     *
     * @return Story
     */
    public function setRandom(bool $random) : Story
    {
        $this->random = $random;

        return $this;
    }

    /**
     * @return int
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @param int $rounds
     *
     * @return Story
     */
    public function setRounds(int $rounds) : Story
    {
        $this->rounds = $rounds;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() : \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return User
     */
    public function getCreatedBy() : User
    {
        return $this->createdBy;
    }

    /**
     * @return User
     */
    public function getUpdatedBy() : User
    {
        return $this->updatedBy;
    }

    /**
     * Hard coded for now. Possible feature to allow creator to choose
     * duration in the future.
     *
     * @return \DateInterval
     */
    public function getSkipTurnDuration() : \DateInterval
    {
        return new \DateInterval('P5D');
    }
}
