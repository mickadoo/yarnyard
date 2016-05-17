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
     * @Groups({"story"})
     *
     * @var User
     */
    private $createdBy;

    /**
     * @Groups({"story"})
     *
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
