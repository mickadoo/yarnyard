<?php

namespace Mickadoo\SearchBundle\Util;

class PropertyParser
{
    const TYPE_IS = '=';
    const TYPE_GREATER = '>';
    const TYPE_GREATER_OR_EQUALS = '>=';
    const TYPE_LESS = '<';
    const TYPE_LESS_OR_EQUALS = '<=';

    /**
     * @param $valueString
     *
     * @return DQLNode[]
     */
    public function parse($valueString) : array
    {
        $nodes = [];
        $parts = explode(',', $valueString);

        foreach ($parts as $part) {
            $nodes[] = $this->getNode($part);
        }

        return $nodes;
    }

    /**
     * @param $part
     *
     * @return DQLNode
     */
    private function getNode($part) : DQLNode
    {
        if (in_array($part[0], ['<', '>'])) {
            return $this->createComparative($part);
        }

        return $this->create(self::TYPE_IS, $part);
    }

    /**
     * @param $part
     *
     * @return DQLNode
     */
    private function createComparative($part) : DQLNode
    {
        // if second char is = assume we're dealing with >= or <=
        $operator = $part[1] === '=' ? substr($part, 0, 2) : substr($part, 0, 1);
        $value = str_replace($operator, '', $part);

        return $this->create($operator, $value);
    }

    /**
     * @param $operator
     * @param $value
     *
     * @return DQLNode
     */
    private function create($operator, $value) : DQLNode
    {
        return new DQLNode($operator, $value);
    }
}
