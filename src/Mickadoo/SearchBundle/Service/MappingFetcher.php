<?php

namespace Mickadoo\SearchBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\MappingException;
use Mickadoo\SearchBundle\Exception\MappingNotFoundException;

class MappingFetcher
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $class
     * @param string $field
     *
     * @return array
     *
     * @throws MappingException
     * @throws \Exception
     */
    public function fetch(string $class, string $field) : array
    {
        $metadata = $this->manager->getClassMetadata($class);

        if ($metadata->hasField($field)) {
            return $metadata->getFieldMapping($field);
        } elseif ($metadata->hasAssociation($field)) {
            return $metadata->getAssociationMapping($field);
        }

        throw new MappingNotFoundException();
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getFields(string $class) : array
    {
        $metadata = $this->manager->getClassMetadata($class);
        $fieldNames = $metadata->getFieldNames();
        $relatedFieldNames = array_keys($metadata->getAssociationMappings());

        return array_unique(array_merge($fieldNames, $relatedFieldNames));
    }
}
