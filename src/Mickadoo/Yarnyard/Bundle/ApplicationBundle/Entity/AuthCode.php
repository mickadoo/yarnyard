<?php
/**
 * Created by PhpStorm.
 * User: mickadoo
 * Date: 08.03.15
 * Time: 14:39
 */

namespace Mickadoo\Yarnyard\Bundle\ApplicationBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Mickadoo\Yarnyard\Bundle\ApplicationBundle\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     */
    protected $user;
}