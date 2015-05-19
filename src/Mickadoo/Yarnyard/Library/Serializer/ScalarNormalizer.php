<?php

namespace Mickadoo\Yarnyard\Library\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Mickadoo\Yarnyard\Library\Annotation\Serializer;
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

        $reflectionObject = new \ReflectionObject(new $className());
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $ignoredAttributes = $this->getIgnoredAttributes($reflectionObject);

        $attributes = array();

        foreach ($reflectionMethods as $method) {

            if ($this->isGetMethod($method)) {
                $attributeName = lcfirst(substr($method->name, 0 === strpos($method->name, 'is') ? 2 : 3));

                if (in_array($attributeName, $ignoredAttributes)) {
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
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed $data data to restore
     * @param string $class the expected class to instantiate
     * @param string $format format the given data was extracted from
     * @param array $context options available to the denormalizer
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return null;
        // TODO: Implement denormalize() method.
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
        return true;
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

        foreach ($reflectionProperties as $property)
        {
            /** @var Serializer $propertyAnnotation */
            $propertyAnnotation = $reader->getPropertyAnnotation($property, Serializer::CLASS_NAME);
            if ($propertyAnnotation && $propertyAnnotation->ignorable) {
                $ignoredAttributes[] = $property->getName();
            }
        }

        return $ignoredAttributes;
    }

}