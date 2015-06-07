<?php

namespace Mickadoo\Yarnyard\Bundle\ApiDocYamlAnnotationBundle\Annotation;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\Routing\Route;

class ApiDocYamlGenerator implements HandlerInterface
{

    const CLASSNAME = __CLASS__;

    public $filename = 'foo';

    public function __construct()
    {
        $filename = 'annotations.yml';

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * Parse route parameters in order to populate ApiDoc.
     *
     * @param ApiDoc $annotation
     * @param array $annotations
     * @param Route $route
     * @param \ReflectionMethod $method
     */
    public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $method)
    {
        $filename = 'annotations.yml';

        $fileHandle = fopen($filename, 'a');
        fwrite($fileHandle, PHP_EOL);
        fwrite($fileHandle, $method->getName() . ':' . PHP_EOL);
        fwrite($fileHandle,"  description: "  . sprintf("'%s'", $annotation->getDescription()) . PHP_EOL);
        fwrite($fileHandle,"  section: "  . sprintf("'%s'", $annotation->getSection()) . PHP_EOL);
        if ($annotation->getRequirements()) {
            fwrite($fileHandle,"  requirements: " . PHP_EOL);

            foreach ($annotation->getRequirements() as $name => $requirement) {
                fwrite($fileHandle,"    -"  . PHP_EOL);
                fwrite($fileHandle,"      name: '$name'"  . PHP_EOL);
                foreach ($requirement as $key => $parameter) {
                    fwrite($fileHandle,"      $key: "  . sprintf("'%s'", $parameter) . PHP_EOL);
                }
            }
        }


        fclose($fileHandle);
    }


}