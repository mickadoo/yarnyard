<?php

namespace YarnyardBundle\Exception\Mapping;

use Symfony\Component\HttpFoundation\Response;
use YarnyardBundle\Exception\Constants\UserErrors;

class UserExceptionCodeMapping implements ExceptionCodeMapperInterface
{
    /**
     * @return array
     */
    public function getMapping()
    {
        return [
            UserErrors::ERROR_USER_EMAIL_ALREADY_EXISTS => Response::HTTP_CONFLICT,
            UserErrors::ERROR_USER_EMAIL_CONTAINS_NON_ASCII => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_EMAIL_NOT_SET => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_EMAIL_INVALID => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_NOT_SET => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_NOT_FOUND => Response::HTTP_NOT_FOUND,
            UserErrors::ERROR_USER_USERNAME_TOO_SHORT => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_TOO_LONG => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_CONTAINS_NON_ASCII => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_USERNAME_ALREADY_EXISTS => Response::HTTP_CONFLICT,
            UserErrors::ERROR_USER_PASSWORD_NOT_SET => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_PASSWORD_TOO_SHORT => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_PASSWORD_TOO_LONG => Response::HTTP_BAD_REQUEST,
            UserErrors::ERROR_USER_PASSWORD_CONTAINS_NON_ASCII => Response::HTTP_BAD_REQUEST,
        ];
    }
}