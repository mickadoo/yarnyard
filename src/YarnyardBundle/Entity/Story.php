<?php

namespace YarnyardBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Story
{
    /**
     * @Groups({"story"})
     *
     * @var int
     */
    private $id;

    /**
     * @Groups({"story"})
     *
     * @var string
     */
    private $title;

    /**
     * @Groups({"story"})
     *
     * @var bool
     */
    private $isCompleted;

    /**
     * @Groups({"story"})
     *
     * @var int
     */
    private $sentenceLimit;

    /**
     * @Groups({"story"})
     *
     * @var int
     */
    private $contributionMode;

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
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * @param bool $isCompleted
     *
     * @return $this
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * @return int
     */
    public function getSentenceLimit()
    {
        return $this->sentenceLimit;
    }

    /**
     * @param int $sentenceLimit
     *
     * @return $this
     */
    public function setSentenceLimit($sentenceLimit)
    {
        $this->sentenceLimit = $sentenceLimit;

        return $this;
    }

    /**
     * @return int
     */
    public function getContributionMode()
    {
        return $this->contributionMode;
    }

    /**
     * @param int $contributionMode
     *
     * @return $this
     */
    public function setContributionMode($contributionMode)
    {
        $this->contributionMode = $contributionMode;

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
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
