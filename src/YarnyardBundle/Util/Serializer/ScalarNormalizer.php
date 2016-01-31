<?php

namespace YarnyardBundle\Util\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use YarnyardBundle\Util\Serializer\Annotation\Serializer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ScalarNormalizer  extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface
{
    const DOC_COMMENT_IGNORE = 'Ignore';

    /**
     * @var array
     */
    protected $config;

    /**
     * @param object $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $className = ClassUtils::getRealClass(get_class($object));

        $reflectionClass = new \ReflectionClass($className);
        $reflectionObject = new \ReflectionObject($reflectionClass->newInstanceWithoutConstructor());
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $ignoredAttributes = $this->getIgnoredAttributes($reflectionObject);

        $attributes = array();

        foreach ($reflectionMethods as $method) {

            if ($this->isGetMethod($method)) {
                $attributeName = lcfirst(substr($method->name, 0 === strpos($method->name, 'is') ? 2 : 3));

                if (in_array($attributeName, $ignoredAttributes)) {
                    continue;
                }

                if (!$reflectionClass->hasProperty($attributeName)) {
                    continue;
                }

                $attributeValue = $method->invoke($object);

                if (null !== $attributeValue && !is_scalar($attributeValue)) {
                    continue;
                }

                $attributes[$attributeName] = $attributeValue;
            }
        }

        return $attributes;
    }

    /**
     * @param mixed $data
     * @param string $class
     * @param null $format
     * @param array $context
     * @return object
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!class_exists($class)) {
            throw new \Exception('Denormalization class does not exist: ' . $class);
        }

        $reflectionClass = new \ReflectionClass($class);

        if (! $reflectionClass->getConstructor() || !$reflectionClass->getConstructor()->getParameters()) {
            $object = $reflectionClass->newInstance();
        } else {
            $object = $reflectionClass->newInstanceWithoutConstructor();
        }

        foreach ($data as $property => $propertyValue) {
            $setter = 'set' . ucfirst($property);

            if ($reflectionClass->hasMethod($setter)) {
                $object->$setter($propertyValue);
            }
        }

        return $object;
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed $data Data to denormalize from.
     * @param string $type The class to which the data should be denormalized.
     * @param string $format The format being deserialized from.
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return true;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data);
    }

    /**
     * Checks if a method's name is get.* or is.*, and can be called without parameters.
     *
     * @param \ReflectionMethod $method the method to check
     *
     * @return bool whether the method is a getter or boolean getter.
     */
    private function isGetMethod(\ReflectionMethod $method)
    {
        $methodLength = strlen($method->name);

        return (
            ((0 === strpos($method->name, 'get') && 3 < $methodLength) ||
                (0 === strpos($method->name, 'is') && 2 < $methodLength)) &&
            0 === $method->getNumberOfRequiredParameters()
        );
    }

    /**
     * @param \ReflectionObject $reflectionObject
     * @return array
     */
    private function getIgnoredAttributes(\ReflectionObject $reflectionObject)
    {
        $reader = new AnnotationReader();
        $ignoredAttributes = [];
        $reflectionProperties = $reflectionObject->getProperties();
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionProperties as $property) {
            /** @var Serializer $propertyAnnotation */
            $propertyAnnotation = $reader->getPropertyAnnotation($property, Serializer::class);
            if ($propertyAnnotation && $propertyAnnotation->ignorable) {
                $ignoredAttributes[] = $property->getName();
            }
        }

        foreach ($reflectionMethods as $method) {

            if (substr($method->getName(), 0, 3) !== 'get') {
                continue;
            }

            $getterAnnotation = $reader->getMethodAnnotation($method, Serializer::class);
            if ($getterAnnotation && $getterAnnotation->ignorable) {
                $ignoredAttributes[] = lcfirst(str_replace('get', '', $method->getName()));
            }
        }

        return $ignoredAttributes;
    }

}