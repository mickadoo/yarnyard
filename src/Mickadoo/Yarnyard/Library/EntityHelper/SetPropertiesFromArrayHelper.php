<?php

namespace Mickadoo\Yarnyard\Library\EntityHelper;

use Mickadoo\Yarnyard\Library\ErrorConstants\Errors;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;

class SetPropertiesFromArrayHelper
{

    /**
     * @param $entity
     * @param array $rawData
     * @throws YarnyardException
     */
    public static function set($entity, array $rawData)
    {
        if (! is_object($entity)) {
            throw new YarnyardException(Errors::ERROR_OBJECT_EXPECTED);
        }

        foreach ($rawData as $propertyName => $propertyValue) {
            $setter = 'set' . ucfirst($propertyName);

            if (is_scalar($propertyValue) && method_exists($entity, $setter)) {
                $entity->$setter($propertyValue);
            }
        }
    }

}