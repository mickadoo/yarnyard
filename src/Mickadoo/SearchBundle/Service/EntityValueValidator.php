<?php

namespace Mickadoo\SearchBundle\Service;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;

class EntityValueValidator
{
    /**
     * @var MappingFetcher
     */
    protected $fetcher;

    /**
     * @param MappingFetcher $fetcher
     */
    public function __construct(MappingFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    /**
     * Check whether a value is valid input for a database field.
     * For related entities it does not check the class, only integers are
     * allowed since only integers are allowed for foreign key fields.
     *
     * @param string $class
     * @param string $field
     * @param $value
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isValid(string $class, string $field, $value) : bool
    {
        if (!$this->hasProperty($class, $field)) {
            return false;
        }

        switch ($this->getExpectedType($class, $field)) {
            case Type::INTEGER:
                return filter_var($value, FILTER_VALIDATE_INT);
            case Type::STRING:
                return is_string($value);
            default:
                throw new \UnexpectedValueException('cannot validate type');
        }
    }

    /**
     * @param string $className
     * @param string $property
     *
     * @return bool
     */
    private function hasProperty(string $className, string $property) : bool
    {
        return in_array($property, $this->fetcher->getFields($className));
    }

    /**
     * Get the expected database type based on field and class.
     *
     * @param string $class
     * @param string $field
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getExpectedType(string $class, string $field) : string
    {
        $mapping = $this->fetcher->fetch($class, $field);

        switch ($mapping['type']) {
            case 'integer':
            case ClassMetadata::ONE_TO_ONE:
            case ClassMetadata::MANY_TO_MANY:
            case ClassMetadata::MANY_TO_ONE:
            case ClassMetadata::ONE_TO_MANY:
                return Type::INTEGER;
            case 'string':
                return Type::STRING;
            default:
                throw new \UnexpectedValueException('unrecognized mapping type');
        }
    }
}
