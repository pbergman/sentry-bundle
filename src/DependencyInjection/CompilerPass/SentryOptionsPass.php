<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection\CompilerPass;

use PBergman\Bundle\SentryBundle\Listener\BeforeBreadcrumbListener;
use PBergman\Bundle\SentryBundle\Listener\BeforeSendListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SentryOptionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('sentry.client.options')) {
            $container
                ->getDefinition('sentry.client.options')
                ->addMethodCall('setBeforeBreadcrumbCallback', [new Reference(BeforeBreadcrumbListener::class)])
                ->addMethodCall('setBeforeSendCallback', [new Reference(BeforeSendListener::class)]);
        }
    }
}