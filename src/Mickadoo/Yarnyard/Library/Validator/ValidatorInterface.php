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
     * @return string
     */
    public function getErrorKey();

}