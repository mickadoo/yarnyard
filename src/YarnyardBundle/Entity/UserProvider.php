<?php

namespace YarnyardBundle\Entity;

use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use YarnyardBundle\Service\UserService;

class UserProvider implements JWTUserProviderInterface
{
    const ANONYMOUS_UUID = 'anonymous';

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserRepository $userRepository
     * @param UserService $userService
     */
    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * @param \stdClass $jwt
     *
     * @return null|User
     */
    public function loadUserByJWT($jwt)
    {
        $user = $this->userRepository->findOneBy(['uuid' => $jwt->userId]);

        if (!$user) {
            $user = $this->userService->create($jwt->userId);
        }

        return $user;
    }

    /**
     * @return User
     */
    public function getAnonymousUser()
    {
        $user = $this->userRepository->findOneBy(['uuid' => self::ANONYMOUS_UUID]);

        if (!$user) {
            $user = new User(self::ANONYMOUS_UUID);
        }

        return $user;
    }

    /**
     * @param string $username
     * @return User
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $query = $this->userRepository
            ->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery();

        try {
            $user = $query->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf('Unable to find an active admin object identified by "%s".', $username);
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * @param UserInterface|User $user
     *
     * @return null|object
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->userRepository->find($user->getId());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->userRepository->getClassName() === $class
        || is_subclass_of($class, $this->userRepository->getClassName());
    }
}
