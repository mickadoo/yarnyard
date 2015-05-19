<?php

namespace Mickadoo\Yarnyard\Library\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class Serializer extends Annotation
{

    const CLASS_NAME = __CLASS__;

    /**
     * @var bool
     */
    public $ignorable;

}
