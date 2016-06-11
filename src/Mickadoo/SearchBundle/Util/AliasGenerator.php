<?php

namespace Mickadoo\SearchBundle\Util;

class AliasGenerator
{
    /**
     * @param string $class
     *
     * @return string
     */
    public function generate(string $class) : string
    {
        $start = strpos($class, '\\') !== false ? strrpos($class, '\\') + 1 : 0;

        return strtolower(substr($class, $start));
    }
}
