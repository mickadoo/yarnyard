<?php

namespace Mickadoo\SearchBundle\Service;

use Doctrine\DBAL\Types\Type;

class DQLValueFormatter
{
    /**
     * @var EntityValueValidator
     */
    protected $validator;

    /**
     * @param EntityValueValidator $validator
     */
    public function __construct(EntityValueValidator $validator)
    {
        $this->validator = $validator;
    }

    public function format(string $class, string $field, $value)
    {
        switch ($this->validator->getExpectedType($class, $field)) {
            case Type::INTEGER:
                return (int) $value;
            case Type::STRING:
                return sprintf("'%s'", $value);
            case Type::BOOLEAN:
                return (int) (bool) $value;
            default:
                throw new \UnexpectedValueException('Unrecognized type');
        }
    }
}
