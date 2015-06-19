<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

class ConfirmationTokenRepository extends EntityRepository
{

    /**
     * @param User $user
     * @return ConfirmationToken
     */
    public function createTokenForUser(User $user)
    {
        $confirmationToken = new ConfirmationToken();

        $confirmationToken
            ->setUser($user)
            ->setCreatedAt(new \DateTime())
            ->setToken(md5($user->getSalt()))
            ->setExpiresAt(new \DateTime('now + 2 weeks'));

        $this->_em->persist($confirmationToken);

        return $confirmationToken;
    }

}