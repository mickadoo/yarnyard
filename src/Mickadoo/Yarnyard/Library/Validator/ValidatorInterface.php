<?php

namespace Mickadoo\Yarnyard\Library\Validator;

interface ValidatorInterface
{

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @param $code
     * @return $this
     */
    public function setErrorCode($code);

    /**
     * @return string
     */
    public function getErrorKey();

    /**
     * @param int $key
     * @return $this
     */
    public function setErrorKey($key);

    /**
     * @param string $code
     * @param int $key
     */
    public function setErrorResponse($code, $key = 400);

}