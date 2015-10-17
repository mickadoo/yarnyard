<?php

namespace Mickadoo\Yarnyard\Bundle\BaseDataBundle\Tests\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand;
use Mickadoo\Yarnyard\Bundle\BaseDataBundle\Command\LoadBaseDataCommand;
use Mickadoo\Yarnyard\Bundle\BaseDataBundle\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Dumper;

class TestLoadBaseDataCommand extends KernelTestCase
{
    /**
     * @param array $configurationData
     * @dataProvider configurationDataProvider
     */
    public function testBasicFunctionality(array $configurationData)
    {
        $className = $configurationData['class'];
        unset($configurationData['class']);

        $kernel = $this->createKernel();
        $kernel->boot();
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $entityRepository = $entityManager->getRepository($className);

        $allExistingIds = array_column(
            $entityRepository->createQueryBuilder('entity')->select('entity.id')->getQuery()->getArrayResult(),
            'id'
        );

        $unlistedIds = array_diff($allExistingIds, array_keys($configurationData));

        $this->createConfigurationFile($configurationData, $className);

        $application = new Application($kernel);
        $application->add(new LoadBaseDataCommand());

        $this->updateSchema($application);

        $command = $application->find('basedata:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'namespace' => 'BaseDataBundle']);

        foreach ($configurationData as $id => $entityData) {
            $entity = $entityManager->find($className, $id);
            $this->assertNotNull($entity);
            foreach ($entityData as $property => $value) {
                $getter = 'get' . ucfirst($property);
                if (method_exists($entity, $getter)) {
                    $this->assertEquals($value, $entity->$getter());
                }
            }
        }

        foreach ($unlistedIds as $id) {
            $this->assertNull($entityManager->find($className, $id));
        }
    }

    /**
     * @param Application $application
     */
    protected function updateSchema(Application $application)
    {
        $application->add(new UpdateSchemaDoctrineCommand());

        $mockOutput = new ConsoleOutput();
        $updateDatabaseCommand = $application->find('doctrine:schema:update');

        $arguments = [
            'command' => 'doctrine:schema:update',
            '--env'    => 'test',
            '--force'  => true,
        ];

        $updateDatabaseCommandInput = new ArrayInput($arguments);
        $updateDatabaseCommand->run($updateDatabaseCommandInput, $mockOutput);
    }

    /**
     * @param array $configurationData
     * @param $className
     */
    private function createConfigurationFile(array $configurationData, $className)
    {
        $reflectionClass = new \ReflectionClass($className);
        $shortName = $reflectionClass->getShortName();

        $dumper = new Dumper();
        $yamlContent = $dumper->dump($configurationData);
        $path = __DIR__ . '/../../Resources/config/doctrine/' . $shortName . '.base.yml';
        file_put_contents($path, $yamlContent);
    }

    public function configurationDataProvider()
    {
        return [
            [
                [
                    'class' => Test::class,
                    0 => [
                        'name' => 'Kevin'
                    ],
                    1 => [
                        'name' => 'Frank'
                    ]
                ]
            ],
            [
                [
                    'class' => Test::class,
                    2 => [
                        'name' => 'Kevin'
                    ],
                    4 => [
                        'name' => 'Frank'
                    ]
                ]
            ]
        ];
    }
}