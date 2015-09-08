<?php

namespace Mickadoo\Yarnyard\Library\Request;

use Mickadoo\Yarnyard\Library\Exception\YarnyardException;

class Patcher
{
    const OP = 'op';
    const PATH = 'path';
    const VALUE = 'value';

    /**
     * @param $entity
     * @param $patchData
     */
    public function patch($entity, $patchData)
    {
        foreach ($patchData as $patch) {
            $this->doPatch($entity, $patch);
        }
    }

    /**
     * @param $entity
     * @param $patch
     */
    protected function doPatch($entity, $patch)
    {
        $this->validatePatch($patch);

        switch ($patch[self::OP]) {
            case RequestConstants::OP_REPLACE:
            case RequestConstants::OP_ADD:
                $this->setValue($entity, $patch);
        }
    }

    /**
     * @param $entity
     * @param $patch
     */
    protected function setValue($entity, $patch)
    {
        $setter = $this->getSetterFromPath($patch[self::PATH]);
        $entity->$setter($patch[self::VALUE]);
    }

    /**
     * @param $path
     * @return string
     */
    protected function getSetterFromPath($path)
    {
        return 'set' . ucfirst(ltrim($path, '/'));
    }

    /**
     * @param $patch
     * @throws YarnyardException
     */
    protected function validatePatch($patch)
    {
        if (!isset($patch[self::OP], $patch[self::PATH], $patch[self::VALUE])) {
            throw new YarnyardException();
        }

        if (!in_array($patch[self::OP], [RequestConstants::OP_ADD, RequestConstants::OP_REPLACE])) {
            throw new YarnyardException();
        }
    }
}