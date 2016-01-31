<?php

namespace YarnyardBundle\Util\String;

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