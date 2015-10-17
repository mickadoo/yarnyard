<?php

namespace Mickadoo\Yarnyard\Bundle\BaseDataBundle\Services;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class BundleHelper
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param null $filter
     * @return array|BundleInterface[]
     */
    public function getBundles($filter = null)
    {
        $bundles = $this->kernel->getBundles();

        $filterFunction = function (Bundle $bundle) use ($filter) {
            return strpos($bundle->getName(), $filter) !== false;
        };

        if (!$filter) {
            return $bundles;
        } else {
            return array_filter($bundles, $filterFunction);
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public function getBundleNameFromPath($path)
    {
        $pattern = "#.*/(\w+Bundle)/.*#";
        preg_match($pattern, $path, $results);

        return isset ($results[1]) ? $results[1] : null;
    }

    /**
     * @param $path
     * @return bool
     */
    public function getBundleNameFromNamespace($path)
    {
        $pattern = "#.*\\\(\w+Bundle)\\\.*#";
        preg_match($pattern, $path, $results);

        return isset ($results[1]) ? $results[1] : null;
    }
}