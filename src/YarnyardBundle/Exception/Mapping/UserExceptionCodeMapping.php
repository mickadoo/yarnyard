<?php

namespace YarnyardBundle\Exception\Mapping;

use FOS\RestBundle\Util\Codes;
use YarnyardBundle\Exception\Constants\UserErrors;

class UserExceptionCodeMapping implements ExceptionCodeMapperInterface
{
    /**
     * @return array
     */
    public function getMapping()
    {
        return [
            UserErrors::ERROR_USER_EMAIL_ALREADY_EXISTS => Codes::HTTP_CONFLICT,
            UserErrors::ERROR_USER_EMAIL_CONTAINS_NON_ASCII => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_EMAIL_NOT_SET => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_EMAIL_INVALID => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_NOT_SET => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_NOT_FOUND => Codes::HTTP_NOT_FOUND,
            UserErrors::ERROR_USER_USERNAME_TOO_SHORT => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_TOO_LONG => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_CONTAINS_NON_ASCII => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_ALREADY_EXISTS => Codes::HTTP_CONFLICT,
            UserErrors::ERROR_USER_PASSWORD_NOT_SET => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_PASSWORD_TOO_SHORT => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_PASSWORD_TOO_LONG => Codes::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_PASSWORD_CONTAINS_NON_ASCII => Codes::HTTP_BAD_REQUEST
        ];
    }
}