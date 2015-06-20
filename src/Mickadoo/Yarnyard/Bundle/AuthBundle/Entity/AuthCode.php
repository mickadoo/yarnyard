<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class AuthCode extends BaseAuthCode
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
     * @var Client
     *
     * @ORM\ManyToOne(
     *  targetEntity="Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\Client"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     */
    protected $user;
}