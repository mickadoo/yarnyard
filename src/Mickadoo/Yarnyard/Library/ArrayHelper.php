<?php

namespace Mickadoo\Yarnyard\Library;

class ArrayHelper
{
    /**
     * @param array $array
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