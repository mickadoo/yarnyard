<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

class RefreshToken extends BaseRefreshToken
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var User
     */
    protected $user;
}