<?php


namespace Mickadoo\Yarnyard\Library\Validator;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

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
     * @return bool
     */
    abstract public function isValid();

    /**
     * @param string $key
     * @param int $code
     */
    public function setErrorResponse($key, $code = 400)
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
    public function setErrorCode($errorCode)
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
    public function setErrorKey($errorKey)
    {
        $this->errorKey = $errorKey;

        return $this;
    }

}