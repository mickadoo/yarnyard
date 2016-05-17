<?php

namespace YarnyardBundle\Exception\Mapping;

interface ExceptionCodeMapperInterface
{
    /**
     * @return array
     */
    public function getMapping();
}
