<?php

use Maxime\UserBundle\MaximeUserBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            // Symfony
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // Extra
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            // Extra Jmoati
            new Jmoati\HelperBundle\JmoatiHelperBundle(),
            new Jmoati\TwitterBootstrapBundle\JmoatiTwitterBootstrapBundle(),
            // Application
            new Jmoati\Gitolite\CoreBundle\JmoatiGitoliteCoreBundle(),
            new Jmoati\Gitolite\ConsoleBundle\JmoatiGitoliteConsoleBundle(),
            new Jmoati\Gitolite\FrontBundle\JmoatiGitoliteFrontBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            // Extra Jmoati
            $bundles[] = new Jmoati\GeneratorBundle\JmoatiGeneratorBundle();
            // Symfony
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
