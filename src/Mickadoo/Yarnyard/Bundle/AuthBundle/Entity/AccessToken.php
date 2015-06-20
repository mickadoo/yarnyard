<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class AccessToken extends BaseAccessToken
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