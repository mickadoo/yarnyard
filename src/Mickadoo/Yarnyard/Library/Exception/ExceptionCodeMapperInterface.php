<?php

namespace Mickadoo\Yarnyard\Library\Exception;

interface ExceptionCodeMapperInterface
{
    /**
     * @return array
     */
    public function getMapping();
}