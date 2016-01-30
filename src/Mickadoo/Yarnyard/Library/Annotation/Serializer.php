<?php

namespace Mickadoo\Yarnyard\Library\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class Serializer extends Annotation
{
    /**
     * @var bool
     */
    public $ignorable;
}
