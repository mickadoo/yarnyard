<?php

namespace Mickadoo\Yarnyard\Library\Exception;

use Exception;

class NonCriticalException extends \RuntimeException
{

    /**
     * @var string
     */
    protected $key;

    public function __construct($key = "", $code = 400)
    {
        $this->key = $key;

        // todo translate key
        $message = $key;

        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

}