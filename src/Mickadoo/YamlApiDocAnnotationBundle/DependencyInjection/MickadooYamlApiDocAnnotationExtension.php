<?php

namespace Mickadoo\YamlApiDocAnnotationBundle\DependencyInjection;

use Mickadoo\YamlApiDocAnnotationBundle\Annotation\ApiDocYamlGenerator;
use Mickadoo\YamlApiDocAnnotationBundle\Command\GenerateYamlForExistingDocBlockCommand;
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
class MickadooYamlApiDocAnnotationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($this->isYamlDocGenerateCommand()) {
            $generatorDefinition = $this->getGeneratorDefinitionClass($configs);
            $container->setDefinition(ApiDocYamlGenerator::SERVICE_ID, $generatorDefinition);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param array $configs
     * @return Definition
     * @throws \Exception
     */
    private function getGeneratorDefinitionClass(array $configs)
    {
        if (!isset($configs[0]['filename'])) {
            throw new \Exception('Filename for generated yaml annotations not defined. Please define it in your config.yml under the mickadoo_yaml_api_doc_annotation node');
        }

        $generatorDefinition = new Definition();
        $generatorDefinition
            ->setClass(ApiDocYamlGenerator::CLASSNAME)
            ->setTags([
                'nelmio_api_doc.extractor.handler' => [[]]
            ])
            ->addMethodCall('setBaseFileName', [$configs[0]['filename']]);

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

        if (!isset($_SERVER['argv'])) {
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
