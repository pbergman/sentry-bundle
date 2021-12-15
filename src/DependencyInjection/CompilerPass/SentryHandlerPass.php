<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection\CompilerPass;

use PBergman\Bundle\SentryBundle\Monolog\SentryHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SentryHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('monolog.handler.sentry')) {
            $container
                ->getDefinition('monolog.handler.sentry')
                ->setClass(SentryHandler::class);
        }
    }
}