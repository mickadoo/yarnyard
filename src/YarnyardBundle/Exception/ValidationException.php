<?php

namespace YarnyardBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

class ValidationException extends YarnyardException
{
    /**
     * @var int
     */
    protected $code = Response::HTTP_BAD_REQUEST;
}
