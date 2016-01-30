<?php

namespace Mickadoo\Yarnyard\Library\Exception;

use FOS\RestBundle\Util\Codes;
use YarnyardBundle\Exception\UserExceptionCodeMapping;

class ExceptionCodeMapper
{
    /**
     * @var ExceptionCodeMapperInterface[]
     */
    private $mappers;

    public function __construct()
    {
        $this->mappers = [
            new UserExceptionCodeMapping(),
        ];
    }

    /**
     * @param \Exception $exception
     * @return int
     */
    public function getCode(\Exception $exception)
    {
        foreach ($this->mappers as $mapper) {
            if (array_key_exists($exception->getMessage(), $mapper->getMapping())) {
                return $mapper->getMapping()[$exception->getMessage()];
            }
        }

        return Codes::HTTP_INTERNAL_SERVER_ERROR;
    }
}