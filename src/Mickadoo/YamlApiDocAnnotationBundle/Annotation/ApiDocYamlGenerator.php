<?php

namespace Mickadoo\YamlApiDocAnnotationBundle\Annotation;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\Routing\Route;

class ApiDocYamlGenerator implements HandlerInterface
{

    const CLASSNAME = __CLASS__;
    const SERVICE_ID = 'mickadoo_yaml_api_doc_annotation.yaml_generation_handler';

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    private $baseFileName;

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
        $fileHandle = fopen($this->getFileName(), 'a');
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
     * @return string
     */
    protected function getFileName()
    {
        return $this->filename;
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

    /**
     * @param string $rootDirectory
     * @return $this
     * @throws \Exception
     */
    public function setFilename($rootDirectory)
    {
        if ($this->filename) {
            throw new \Exception('Filename can only be set implicitly when running the generation command');
        }

        $fileName = $rootDirectory . '/' . $this->baseFileName;

        if (! file_exists(dirname($fileName))) {
            throw new \Exception('Error, directory to save output does not exist: ' . dirname($fileName));
        }

        $this->filename = $fileName ;

        return $this;
    }

}