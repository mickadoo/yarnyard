<?php

namespace YarnyardBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Sentence
{
    /**
     * @Groups({"sentence"})
     *
     * @var int
     */
    private $id;

    /**
     * @Groups({"sentence"})
     *
     * @var string
     */
    private $text;

    /**
     * @var Story
     */
    private $story;

    /**
     * @Groups({"sentence"})
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Groups({"sentence"})
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     *
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     *
     * @return $this
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
