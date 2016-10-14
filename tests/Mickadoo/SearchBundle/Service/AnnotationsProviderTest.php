<?php

namespace Tests\Mickadoo\SearchBundle\Service;

use Mickadoo\SearchBundle\Service\MappingFetcher;
use Mickadoo\SearchBundle\Service\QueryParamAnnotationHandler;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Prophecy\Argument;
use Symfony\Component\Routing\Route;

class AnnotationsProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function ifClassNotExistsDoNothing()
    {
        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->getFields(Argument::any())->shouldNotBeCalled();
        $fetcher->fetch(Argument::any(), Argument::any())->shouldNotBeCalled();
        $provider = new QueryParamAnnotationHandler($fetcher->reveal());

        $annotation = $this->prophesize(ApiDoc::class);
        $annotation->getResourceDescription()->willReturn('DoesNotExist');
        $route = $this->prophesize(Route::class);
        $method = $this->prophesize(\ReflectionMethod::class);

        $provider->handle(
            $annotation->reveal(),
            [$annotation],
            $route->reveal(),
            $method->reveal()
        );
    }

    /**
     * @test
     */
    public function ifNoMappingExistsDoNothing()
    {
        $class = \stdClass::class;

        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->hasMapping($class)->willReturn(false);
        $fetcher->getFields(Argument::any())->shouldNotBeCalled();
        $fetcher->fetch(Argument::any(), Argument::any())->shouldNotBeCalled();
        $provider = new QueryParamAnnotationHandler($fetcher->reveal());

        $annotation = $this->prophesize(ApiDoc::class);
        $annotation->getResourceDescription()->willReturn($class);
        $route = $this->prophesize(Route::class);
        $method = $this->prophesize(\ReflectionMethod::class);

        $provider->handle(
            $annotation->reveal(),
            [$annotation],
            $route->reveal(),
            $method->reveal()
        );
    }

    /**
     * @test
     */
    public function parametersWilBeAdded()
    {
        $class = \stdClass::class;

        $fieldMapping = [
            'id' => 'integer',
            'name' => 'string',
            'author' => 2,
        ];

        $annotation = $this->prophesize(ApiDoc::class);
        $annotation->getResourceDescription()->willReturn($class);
        $route = $this->prophesize(Route::class);
        $method = $this->prophesize(\ReflectionMethod::class);

        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->hasMapping($class)->willReturn(true);
        $fetcher->getFields($class)->willReturn(array_keys($fieldMapping));

        foreach ($fieldMapping as $name => $type) {
            $fetcher->fetch($class, $name)->willReturn(['type' => $type]);

            if (is_int($type)) {
                $name .= 'Id';
                $type = 'integer';
            }

            $paramData = [
                'name' => $name,
                'dataType' => $type,
                'required' => false,
            ];

            $annotation->addParameter($name, $paramData)->shouldBeCalled();
        }

        $provider = new QueryParamAnnotationHandler($fetcher->reveal());

        $provider->handle(
            $annotation->reveal(),
            [$annotation],
            $route->reveal(),
            $method->reveal()
        );
    }
}
