<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection\CompilerPass;

use PBergman\Bundle\SentryBundle\Listener\BeforeBreadcrumb;
use PBergman\Bundle\SentryBundle\Listener\BeforeSend;
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
                ->addMethodCall('setBeforeBreadcrumbCallback', [new Reference(BeforeBreadcrumb::class)])
                ->addMethodCall('setBeforeSendCallback', [new Reference(BeforeSend::class)]);
        }
    }
}