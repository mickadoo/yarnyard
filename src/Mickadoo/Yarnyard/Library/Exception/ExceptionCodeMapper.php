<?php

namespace Mickadoo\Yarnyard\Library\Exception;

use FOS\RestBundle\Util\Codes;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Exception\AuthExceptionCodeMapper;
use Mickadoo\Yarnyard\Bundle\UserBundle\Exception\UserExceptionCodeMapping;

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
            new AuthExceptionCodeMapper()
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