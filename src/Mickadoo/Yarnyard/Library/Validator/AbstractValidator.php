<?php


namespace Mickadoo\Yarnyard\Library\Validator;

use FOS\OAuthServerBundle\Model\Token;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractValidator implements ValidatorInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        /** @var Token $token */
        $token = $this->container->get('security.token_storage')->getToken();
        /** @var User $user */
        $user = $token->getUser();
        $this->setCurrentUser($user);

        $this->setErrorResponse('ERROR');
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
     */
    protected function getCurrentUser()
    {
        return $this->currentUser;
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
     * @param $string
     * @return bool
     */
    protected function isStringAsciiOnly($string)
    {
        return 0 == preg_match('/[^\x00-\x7F]/', $string);

    }

}