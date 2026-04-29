<?php

namespace Druidvav\EssentialsBundle;

use Druidvav\EssentialsBundle\DependencyInjection\Compiler\ContainerAwarePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DvEssentialsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ContainerAwarePass());
    }
}
