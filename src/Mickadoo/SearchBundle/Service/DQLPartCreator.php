<?php

namespace Mickadoo\SearchBundle\Service;

use Mickadoo\SearchBundle\Util\AliasGenerator;
use Mickadoo\SearchBundle\Util\DQLNode;

class DQLPartCreator
{
    const OPERATOR_OR = 'OR';
    const OPERATOR_AND = 'AND';

    /**
     * @var AliasGenerator
     */
    protected $aliasGenerator;

    /**
     * @var DQLValueFormatter
     */
    protected $formatter;

    /**
     * @var string
     */
    protected $strategy = self::OPERATOR_OR;

    /**
     * @param AliasGenerator    $aliasGenerator
     * @param DQLValueFormatter $formatter
     */
    public function __construct(
        AliasGenerator $aliasGenerator,
        DQLValueFormatter $formatter
    ) {
        $this->aliasGenerator = $aliasGenerator;
        $this->formatter = $formatter;
    }

    /**
     * @param DQLNode[] $nodes
     * @param string    $class
     * @param string    $field
     *
     * @return string
     */
    public function create(array $nodes, string $class, string $field) : string
    {
        $inDql = $this->getInDQL($nodes, $class, $field);
        $rangeDql = $this->getRangeDQL($nodes, $class, $field);

        if ($inDql && !$rangeDql) {
            return $inDql;
        } elseif ($rangeDql && !$inDql) {
            return trim($rangeDql, '()');
        } else {
            return sprintf('%s %s %s', $inDql, $this->strategy, $rangeDql);
        }
    }

    /**
     * @param DQLNode[] $nodes
     * @param string    $class
     * @param string    $field
     *
     * @return string
     */
    private function getInDQL(array $nodes, string $class, string $field) : string
    {
        $column = $this->getColumnName($class, $field);
        $inDql = '';
        $equalsNodes = array_filter($nodes, function (DQLNode $node) {
            return $node->getOperator() === '=';
        });

        if (empty($equalsNodes)) {
            return $inDql;
        }

        $values = array_map(
            function (DQLNode $node) use ($class, $field) {
                return $this->formatter->format($class, $field, $node->getValue());
            },
            $equalsNodes
        );

        return sprintf('%s IN (%s)', $column, implode(',', $values));
    }

    /**
     * @param string $class
     * @param string $field
     *
     * @return string
     */
    private function getColumnName(string $class, string $field): string
    {
        $alias = $this->aliasGenerator->generate($class);

        return sprintf('%s.%s', $alias, $field);
    }

    /**
     * @param DQLNode[] $nodes
     * @param string    $class
     * @param string    $field
     *
     * @return string
     */
    private function getRangeDQL(array $nodes, string $class, string $field) : string
    {
        $rangeDql = '';
        $comparatorNodes = array_filter($nodes, function (DQLNode $node) {
            return in_array($node->getOperator()[0], ['<', '>']);
        });

        if (empty($comparatorNodes)) {
            return $rangeDql;
        }

        $ranges = $this->getRanges($comparatorNodes);

        foreach ($ranges as $range) {
            if (count($range) === 1) {
                $rangeDql .= $this->getSingleRangeDql($class, $field, array_shift($range));
            } else {
                $rangeDql .= $this->getDualRangeDql($class, $field, $range);
            }
        }

        // remove superfluous OR
        return substr($rangeDql, 2 + strlen($this->strategy));
    }

    /**
     * @param DQLNode[] $nodes
     *
     * @return array
     */
    private function getRanges(array $nodes)
    {
        $ranges = [];
        $currentRange = [];

        // sort by value
        uasort($nodes, function (DQLNode $nodeA, DQLNode $nodeB) {
            return $nodeA->getValue() <=> $nodeB->getValue();
        });

        foreach ($nodes as $index => $node) {
            $operatorType = $node->getOperator()[0];
            $isLast = $node === end($nodes);
            $nextOperatorType = isset($nodes[$index + 1]) ?
                $nodes[$index + 1]->getOperator()[0] : null;

            // if next is the same type then ignore current
            if ($operatorType !== $nextOperatorType) {
                $currentRange[$operatorType] = $node;
            }

            // if upper range was just set range is complete, or if last node
            if ($operatorType === '<' && isset($currentRange['<']) || $isLast) {
                $ranges[] = $currentRange;
                $currentRange = [];
            }
        }

        return $ranges;
    }

    /**
     * @param string  $class
     * @param string  $field
     * @param DQLNode $part
     *
     * @return string
     */
    private function getSingleRangeDql(string $class, string $field, DQLNode $part) : string
    {
        $column = $this->getColumnName($class, $field);

        return sprintf(
            ' %s (%s %s %s)',
            $this->strategy,
            $column,
            $part->getOperator(),
            $this->formatter->format($class, $field, $part->getValue())
        );
    }

    /**
     * @param string    $class
     * @param string    $field
     * @param DQLNode[] $range
     *
     * @return string
     */
    private function getDualRangeDql(string $class, string $field, array $range) : string
    {
        $lowerRange = $range['>'];
        $upperRange = $range['<'];
        $column = $this->getColumnName($class, $field);

        return sprintf(
            ' %s (%s %s %s AND %s %s %s)',
            $this->strategy,
            $column,
            $lowerRange->getOperator(),
            $this->formatter->format($class, $field, $lowerRange->getValue()),
            $column,
            $upperRange->getOperator(),
            $this->formatter->format($class, $field, $upperRange->getValue())
        );
    }
}
