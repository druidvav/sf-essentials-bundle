<?php

namespace Druidvav\EssentialsBundle\DependencyInjection\Compiler;

use Druidvav\EssentialsBundle\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContainerAwarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            $this->processDefinition($container, $definition);
        }
    }

    private function processDefinition(ContainerBuilder $container, Definition $definition)
    {
        if ($definition->isAbstract() || $definition->isSynthetic()) {
            return;
        }

        $class = $definition->getClass();
        if (!$class || !class_exists($class)) {
            return;
        }

        $traits = class_uses($class, true);
        if (!isset($traits[ContainerAwareTrait::class])) {
            return;
        }

        $definition->addMethodCall('setContainer', [new Reference('service_container')]);
    }
}
