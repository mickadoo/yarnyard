<?php
/**
 * Created by PhpStorm.
 * User: mickadoo
 * Date: 08.03.15
 * Time: 14:37
 */

namespace Mickadoo\Yarnyard\Bundle\ApplicationBundle\Entity;

    use FOS\OAuthServerBundle\Entity\Client as BaseClient;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
}