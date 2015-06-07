<?php

namespace Mickadoo\YamlApiDocAnnotationBundle\Annotation;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\Routing\Route;

class ApiDocYamlGenerator implements HandlerInterface
{

    const CLASSNAME = __CLASS__;

    public $filename = 'foo';

    /**
     * @var string
     */
    private $baseFileName;

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
        fwrite($fileHandle, "  description: " . sprintf("'%s'", $annotation->getDescription()) . PHP_EOL);
        fwrite($fileHandle, "  section: " . sprintf("'%s'", $annotation->getSection()) . PHP_EOL);
        if ($annotation->getRequirements()) {
            fwrite($fileHandle, "  requirements: " . PHP_EOL);

            foreach ($annotation->getRequirements() as $name => $requirement) {
                fwrite($fileHandle, "    -" . PHP_EOL);
                fwrite($fileHandle, "      name: '$name'" . PHP_EOL);
                foreach ($requirement as $key => $parameter) {
                    fwrite($fileHandle, "      $key: " . sprintf("'%s'", $parameter) . PHP_EOL);
                }
            }
        }

        fclose($fileHandle);
    }

    /**
     * @param string $baseFilename
     * @return $this
     * @throws \Exception
     */
    public function setBaseFileName($baseFilename)
    {
        if (! $this->baseFileName) {
            $this->baseFileName = $baseFilename;
        } else {
            throw new \Exception('Base filename can only be set at boot-time (defined in config.yml)');
        }

        return $this;
    }

}