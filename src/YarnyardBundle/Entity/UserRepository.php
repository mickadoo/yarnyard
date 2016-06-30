<?php

namespace YarnyardBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use YarnyardBundle\Exception\NoAvailableUserException;

class UserRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return User
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);

        return $user;
    }

    /**
     * @param array $exclude
     *
     * @return User
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findRandom($exclude = []) : User
    {
        $numUsers = $this->getCount();

        // filter the excluded users to those that really exist
        if ($exclude) {
            $exclude = $this->filterRealIds($exclude);
        }

        // offset should be (total - excluded - 1)
        $offset = $numUsers - count($exclude) - 1;

        if ($offset < 0) {
            throw new NoAvailableUserException('Negative offset - not enough users');
        }

        $first = mt_rand(0, $offset);

        $query = $this->createQueryBuilder('user');
        $query->orderBy('user.id', 'ASC');

        if (!empty($exclude)) {
            $query->where($query->expr()->notIn('user.id', $exclude));
        }

        $query
            ->setMaxResults(1)
            ->setFirstResult($first);

        return $query->getQuery()->getSingleResult();
    }

    /**
     * @return int
     */
    public function getCount() : int
    {
        $query = $this->createQueryBuilder('user');
        $query->select('COUNT(user.id)');

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $userIds
     *
     * @return array
     */
    public function filterRealIds(array $userIds) : array
    {
        $filterQuery = $this->createQueryBuilder('user');
        $filterQuery->select('user.id');
        $filterQuery->where($filterQuery->expr()->in('user.id', $userIds));
        $result = $filterQuery->getQuery()->getResult();

        return array_column($result, 'id');
    }
}
