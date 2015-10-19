<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * @var int
     */
    protected $id;

}