<?php

namespace YarnyardBundle\Exception\Mapping;

use Symfony\Component\HttpFoundation\Response;

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
     *
     * @return int
     */
    public function getCode(\Exception $exception)
    {
        foreach ($this->mappers as $mapper) {
            if (array_key_exists($exception->getMessage(), $mapper->getMapping())) {
                return $mapper->getMapping()[$exception->getMessage()];
            }
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
