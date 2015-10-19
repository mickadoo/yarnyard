<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

class AuthCode extends BaseAuthCode
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