<?php

/*
 * This file is part of Miguel Simões generic packages.
 *
 * (c) Miguel Simões <msimoes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Implementation of the applications Kernel
 *
 * @author Miguel Simões <msimoes@gmail.com>
 */
class ApplicationKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return dirname(__DIR__). '/var/cache/'. $this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/log';
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            #
            # SensioLabs bundles required by the application
            new Sensio\Bundle\DistributionBundle\SensioDistributionBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            #
            # Symfony bundles required by the application
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            #
            # The specific application bundle initialization
            new Application\ApplicationBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            /* Under the development and testing environment, we want some other bundles to be enabled */
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\TwigBundle\TwigBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ .'/config/' . $this->getEnvironment() . '/config.yml');
    }
}
