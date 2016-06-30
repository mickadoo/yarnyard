<?php

namespace YarnyardBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

class NoAvailableUserException extends YarnyardException
{
    /**
     * @var int
     */
    protected $code = Response::HTTP_CONFLICT;
}
