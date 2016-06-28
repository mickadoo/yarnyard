<?php

namespace YarnyardBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

class YarnyardException extends \Exception
{
    /**
     * @var array
     */
    protected $context;

    /**
     * @var int
     */
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @param string $message
     * @param array  $context
     */
    public function __construct($message, $context = [])
    {
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
