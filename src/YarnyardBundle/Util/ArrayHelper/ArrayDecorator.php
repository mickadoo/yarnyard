<?php

namespace YarnyardBundle\Util\ArrayHelper;

class ArrayDecorator
{
    /**
     * @param array $array
     *
     * @return array
     */
    public function decorateKeys(array $array)
    {
        foreach ($array as $key => $value) {
            unset($array[$key]);
            $array['%' . $key . '%'] = $value;
        }

        return $array;
    }
}
