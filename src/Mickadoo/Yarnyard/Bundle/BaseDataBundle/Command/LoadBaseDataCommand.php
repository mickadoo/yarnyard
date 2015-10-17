<?php

namespace Mickadoo\Yarnyard\Bundle\BaseDataBundle\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Mickadoo\Yarnyard\Bundle\BaseDataBundle\Services\BundleHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class LoadBaseDataCommand extends ContainerAwareCommand
{
    const PATH_IN_BUNDLE = '/Resources/config/doctrine/';
    const CONFIG_PATH_SUFFIX = '.base.yml';

    /**
     * @var \ReflectionClass[]
     */
    private $entityClassesByBundle;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var BundleHelper
     */
    private $bundleHelper;

    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this->setName('basedata:load');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->bundleHelper = $this->getContainer()->get('bundle.helper');

        /** @var ClassMetadata $metaData */
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $metaData) {
            $bundleName = $this->bundleHelper->getBundleNameFromNamespace($metaData->getName());
            $this->entityClassesByBundle[$bundleName][] = $metaData->getReflectionClass();
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws DBALException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFilePaths = $this->getConfigFiles();

        foreach ($configFilePaths as $configFilePath) {

            $configData = Yaml::parse($configFilePath);
            $reflectionClass = $this->getReflectionClassForConfig($configFilePath);

            $this->removeUnlistedEntities(array_keys($configData), $reflectionClass->getName());

            foreach ($configData as $id => $entityData) {

                $entity = $this->entityManager->find($reflectionClass->getName(), $id);
                if (!$entity) {
                    $entity = $this->getNewInstanceFromReflectionClass($reflectionClass, $id);
                }

                $this->updateEntity($entity, $entityData);
            }
        }

        return 0;
    }

    /**
     * @param array $listedIds
     * @param string $className
     * @throws DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Exception
     */
    private function removeUnlistedEntities(array $listedIds, $className)
    {
        $entityRepository = $this->entityManager->getRepository($className);

        $allIds = array_column(
            $entityRepository->createQueryBuilder('entity')->select('entity.id')->getQuery()->getArrayResult(),
            'id'
        );

        $unlistedIds = array_diff($allIds, $listedIds);

        foreach ($unlistedIds as $id) {
            $entityToBeRemoved = $this->entityManager->find($className, $id);
            $this->entityManager->remove($entityToBeRemoved);
            try {
                $this->entityManager->flush($entityToBeRemoved);
            } catch (DBALException $exception) {
                $this->output->writeln(
                    "Entity removal failed. You can remove entities if they have foreign key relations!"
                );

                throw $exception;
            }
        }
    }

    /**
     * @return array
     */
    private function getConfigFiles()
    {
        $configFiles = [];
        $bundles = $this->bundleHelper->getBundles('Mickadoo');

        foreach ($bundles as $bundle) {
            $configPath = $bundle->getPath() . self::PATH_IN_BUNDLE;
            if (file_exists($configPath)) {
                $loadedConfigFiles = glob($configPath . '*' . self::CONFIG_PATH_SUFFIX);
                $configFiles = array_merge($loadedConfigFiles,$configFiles);
            }
        }

        return $configFiles;
    }

    /**
     * @param $configFilePath
     * @return null|\ReflectionClass
     */
    private function getReflectionClassForConfig($configFilePath)
    {
        $bundleName = $this->bundleHelper->getBundleNameFromPath($configFilePath);
        $endOfPath = substr($configFilePath, strpos($configFilePath, self::PATH_IN_BUNDLE));
        $configClassShortName = str_replace([self::PATH_IN_BUNDLE, self::CONFIG_PATH_SUFFIX], '', $endOfPath);

        if (!isset($this->entityClassesByBundle[$bundleName])) {
            return null;
        }

        /** @var \ReflectionClass $reflectionClass */
        foreach ($this->entityClassesByBundle[$bundleName] as $reflectionClass) {
            if ($configClassShortName === $reflectionClass->getShortName()) {
                return $reflectionClass;
            }
        }

        return null;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param $id
     * @return object
     * @throws MappingException
     * @throws \Exception
     */
    private function getNewInstanceFromReflectionClass(\ReflectionClass $reflectionClass, $id)
    {
        $entity = $reflectionClass->newInstanceWithoutConstructor();
        $entity->setId($id);

        /** @var ClassMetadata $metadata */
        $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($reflectionClass->getName());
        $metadata->generatorType = ClassMetadata::GENERATOR_TYPE_NONE;

        $this->entityManager->persist($entity);

        return $entity;
    }

    /**
     * @param $entity
     * @param $entityData
     */
    private function updateEntity($entity, $entityData)
    {
        $hasChanges = false;
        foreach ($entityData as $property => $value) {
            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);

            if (method_exists($entity, $setter) && method_exists($entity, $getter)) {
                $currentValue = $entity->$getter();
                if ($currentValue !== $value) {
                    $entity->$setter($value);
                    $hasChanges = true;
                }
            }
        }

        if ($hasChanges) {
            $this->entityManager->flush($entity);
        }
    }
}