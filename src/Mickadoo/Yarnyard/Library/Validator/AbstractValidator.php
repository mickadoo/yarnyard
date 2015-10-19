<?php


namespace Mickadoo\Yarnyard\Library\Validator;

use FOS\OAuthServerBundle\Model\Token;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\EntityHelper\RepositoryTrait;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractValidator implements ValidatorInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;
    use RepositoryTrait;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * @var string
     */
    protected $errorKey;

    /**
     * @var User
     */
    protected $currentUser;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);

        /** @var Token $token */
        $token = $this->container->get('security.token_storage')->getToken();
        /** @var User $user */
        if ($token) {
            $user = $token->getUser();
            $this->setCurrentUser($user);
        }

        $this->setRequest($this->container->get('request'));
    }

    /**
     * @return bool
     */
    abstract public function isValid();

    /**
     * @param string $key
     * @param int $code
     */
    protected function setErrorResponse($key, $code = 400)
    {
        $this->setErrorKey($key);
        $this->setErrorCode($code);
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param $errorCode
     * @return $this
     */
    protected function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorKey()
    {
        return $this->errorKey;
    }

    /**
     * @param string $errorKey
     * @return $this
     */
    protected function setErrorKey($errorKey)
    {
        $this->errorKey = $errorKey;

        return $this;
    }

    /**
     * @return User
     * @throws YarnyardException
     */
    protected function getCurrentUser()
    {
        if ($this->currentUser) {
            return $this->currentUser;
        }

        throw new YarnyardException("Current user not set. Request is not authenticated?");
    }

    /**
     * @param User $currentUser
     * @return $this
     */
    private function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;

        return $this;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this
     */
    private function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param $string
     * @return bool
     */
    protected function isStringAsciiOnly($string)
    {
        return 0 == preg_match('/[^\x00-\x7F]/', $string);
    }

    /**
     * @param $key
     * @return bool
     */
    protected function requestContains($key)
    {
        return $this->getRequest()->request->has($key);
    }

}
