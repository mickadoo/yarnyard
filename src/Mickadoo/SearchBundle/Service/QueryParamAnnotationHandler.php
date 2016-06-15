<?php

namespace Mickadoo\SearchBundle\Service;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\Routing\Route;

class QueryParamAnnotationHandler implements HandlerInterface
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
     * @param ApiDoc            $annotation
     * @param array             $annotations
     * @param Route             $route
     * @param \ReflectionMethod $method
     */
    public function handle(
        ApiDoc $annotation,
        array $annotations,
        Route $route,
        \ReflectionMethod $method
    ) {
        $resourceClass = $annotation->getResourceDescription();

        if (!class_exists($resourceClass)) {
            return;
        }

        if (!$this->fetcher->hasMapping($resourceClass)) {
            return;
        }

        foreach ($this->fetcher->getFields($resourceClass) as $field) {
            $mapping = $this->fetcher->fetch($resourceClass, $field);

            // integer types are used for doctrine entity mapping
            if (is_int($mapping['type'])) {
                $mapping['type'] = 'integer';
                $field .= 'Id';
            }

            $paramData = [
                'name' => $field,
                'dataType' => $mapping['type'],
                'required' => false,
            ];

            $annotation->addParameter($field, $paramData);
        }
    }
}
