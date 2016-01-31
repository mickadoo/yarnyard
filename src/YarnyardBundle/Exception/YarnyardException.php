<?php

namespace YarnyardBundle\Exception;

class YarnyardException extends \Exception
{
    /**
     * @var array
     */
    protected $context;

    /**
     * @param string $message
     * @param array $context
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