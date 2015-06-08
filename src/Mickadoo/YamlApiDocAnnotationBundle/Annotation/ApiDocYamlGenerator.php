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
        // if no filename then the generator service is probably just cached and trying to be run
        // todo: fix caching of service problem, only inject generator when run from command
        if (! $this->getFileName()) {
            return;
        }

        $fileHandle = fopen($this->getFileName(), 'a');

        fwrite($fileHandle, PHP_EOL);
        fwrite($fileHandle, $method->getName() . ':' . PHP_EOL);
        $this->writeSimpleValues($fileHandle, $annotation);
        $this->writeArrayValues($fileHandle, $annotation);
        fclose($fileHandle);
    }

    /**
     * @param $handle
     * @param ApiDoc $annotation
     */
    private function writeSimpleValues($handle, ApiDoc $annotation)
    {
        $keys = ['description', 'section', 'https', 'resource', 'input', 'output', 'link', 'deprecated'];

        foreach ($keys as $key) {
            $getter = 'get' . ucfirst($key);
            if (method_exists($annotation, $getter) && ! in_array($annotation->$getter(), [null, false])) {
                $value = $annotation->$getter();
                fwrite($handle, sprintf("  %s: '%s'" . PHP_EOL, $key, $value));
            }
        }
    }

    /**
     * @param $handle
     * @param ApiDoc $annotation
     */
    private function writeArrayValues($handle, ApiDoc $annotation)
    {
        $values = ['requirements', 'views', 'parameters', 'statusCodes'];

        foreach ($values as $value) {
            $getter = 'get' . ucfirst($value);
            if (method_exists($annotation, $getter) && $annotation->$getter() !== null && is_array($annotation->$getter()) && ! $this->isEmptyArray($annotation->$getter())) {
                fwrite($handle, sprintf("  %s: " . PHP_EOL, $value));
                foreach ($annotation->$getter() as $key => $arrayValue) {
                    if (is_array($arrayValue) && ! $this->isEmptyArray($arrayValue)) {
                        fwrite($handle, "    -" . PHP_EOL);
                        fwrite($handle, sprintf("      name: '%s'", $key) . PHP_EOL);
                        foreach ($arrayValue as $name => $nestedValue) {
                            fwrite($handle, sprintf("      %s: '%s'", $name, $nestedValue) . PHP_EOL);
                        }
                    } elseif (!is_array($arrayValue)) {
                        fwrite($handle, sprintf("    - '%s'", $arrayValue) . PHP_EOL);
                    }
                }
            }
        }
    }

    /**
     * @param array $array
     * @return bool
     */
    private function isEmptyArray(array $array)
    {
        foreach ($array as $value) {
            if ($value) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getFileName()
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