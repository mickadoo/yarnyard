<?php

namespace Mickadoo\SearchBundle\Exception;

class MappingNotFoundException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'No mapping for that field';
}
