<?php

namespace Mickadoo\YamlApiDocAnnotationBundle\Annotation;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;

class ApiDocYamlAnnotation implements HandlerInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    const ANNOTATION_FILE_PATH = '/config/annotations.yml';

    /**
     * @var array
     */
    protected $annotationSettings;

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
        $settings = $this->getAnnotationSettingsForMethod($method->getName());

        if ($settings) {
            $annotation->__construct($settings);
        }
    }

    /**
     * @param string $methodName
     * @return null
     * @throws \Exception
     */
    private function getAnnotationSettingsForMethod($methodName)
    {
        if (!isset($this->annotationSettings)) {
            $this->loadAnnotationSettings();
        }
        $targetSettings = isset($this->annotationSettings[$methodName]) ? $this->annotationSettings[$methodName] : null;

        return $targetSettings;
    }

    /**
     * @throws \Exception
     */
    private function loadAnnotationSettings()
    {
        $annotationConfigPath = $this->container->get('kernel')->getRootDir() . self::ANNOTATION_FILE_PATH;

        if (!file_exists($annotationConfigPath)) {
            throw new \Exception('Annotation config file not found');
        }

        $this->annotationSettings = Yaml::parse(file_get_contents($annotationConfigPath));
    }

}