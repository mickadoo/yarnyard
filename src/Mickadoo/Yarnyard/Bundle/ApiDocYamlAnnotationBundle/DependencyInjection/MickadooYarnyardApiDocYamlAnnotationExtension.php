<?php

namespace Mickadoo\Yarnyard\Bundle\ApiDocYamlAnnotationBundle\DependencyInjection;

use Mickadoo\Yarnyard\Bundle\ApiDocYamlAnnotationBundle\Annotation\ApiDocYamlGenerator;
use Mickadoo\Yarnyard\Bundle\ApiDocYamlAnnotationBundle\Command\GenerateYamlForExistingDocBlockCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MickadooYarnyardApiDocYamlAnnotationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);


        if ($this->isYamlDocGenerateCommand()) {
            $generatorDefinition = $this->getGeneratorDefinitionClass();
            $container->setDefinition('api_doc.annotation.yaml_generation_handler', $generatorDefinition);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @return Definition
     */
    private function getGeneratorDefinitionClass()
    {
        $generatorDefinition = new Definition();
        $generatorDefinition->setClass(ApiDocYamlGenerator::CLASSNAME);
        $generatorDefinition->setTags([
            'nelmio_api_doc.extractor.handler' => [[]]
        ]);

        return $generatorDefinition;
    }

    /**
     * @return bool
     */
    private function isYamlDocGenerateCommand()
    {
        if (PHP_SAPI !== 'cli') {
            return false;
        }

        if (! isset($_SERVER['argv'])) {
            return false;
        }

        $args = $_SERVER['argv'];

        if (count($args) < 2) {
            return false;
        }

        if ($args[0] !== 'app/console') {
            return false;
        }

        if ($args[1] !== GenerateYamlForExistingDocBlockCommand::NAME) {
            return false;
        }

        return true;
    }
}
