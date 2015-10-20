<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Exception;

use FOS\RestBundle\Util\Codes;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Constants\AuthErrors;
use Mickadoo\Yarnyard\Library\Exception\ExceptionCodeMapperInterface;

class AuthExceptionCodeMapper implements ExceptionCodeMapperInterface
{
    /**
     * @return array
     */
    public function getMapping()
    {
        return [
            AuthErrors::ERROR_AUTH_ACCESS_TOKEN_NOT_FOUND => Codes::HTTP_NOT_FOUND
        ];
    }
}