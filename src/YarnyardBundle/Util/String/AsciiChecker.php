<?php

namespace YarnyardBundle\Util\String;

class AsciiChecker
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