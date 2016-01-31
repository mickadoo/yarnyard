<?php

namespace YarnyardBundle\Util\Serializer\Annotation;

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
