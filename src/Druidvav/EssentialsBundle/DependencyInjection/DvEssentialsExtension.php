<?php

namespace Druidvav\EssentialsBundle\DependencyInjection;

use Druidvav\EssentialsBundle\Twig\Autolink;
use Druidvav\EssentialsBundle\Twig\Basic;
use Druidvav\EssentialsBundle\Twig\Bootstrap5FormExtension;
use Druidvav\EssentialsBundle\Twig\Currency;
use Druidvav\EssentialsBundle\Twig\Grunt;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Extension\Extension;

class DvEssentialsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setParameter('dv_essentials.grunt_asset_manifest_path', '%kernel.project_dir%/config/assets.json');

        $container
            ->register(Basic::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);

        $container
            ->register(Grunt::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addMethodCall('setGruntAssetManifestPath', [new Parameter('dv_essentials.grunt_asset_manifest_path')])
            ->addMethodCall('setProjectDir', [new Parameter('kernel.project_dir')])
            ->addMethodCall('setDebug', [new Parameter('kernel.debug')]);

        $container
            ->register(Currency::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);

        $container
            ->register(Autolink::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);

        $container
            ->register(Bootstrap5FormExtension::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);
    }
}
