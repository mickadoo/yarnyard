<?php

namespace Mickadoo\Yarnyard\Library\StringHelper;

class StringHelper
{
    /**
     * @param $string
     * @return bool
     */
    public function isAsciiOnly($string)
    {
        return 0 == preg_match('/[^\x00-\x7F]/', $string);
    }
}