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
//        $container->getAutoconfiguredInstanceof()

        if ($container->hasDefinition('monolog.handler.sentry')) {
            $definition = $container->getDefinition('monolog.handler.sentry');
            $params     = $container->getParameter('pbergman.sentry_handler.options');
            $definition
                ->setClass(SentryHandler::class)
                ->addMethodCall('setAddTagsToScope', [$params['scope']['add_tags']])
                ->addMethodCall('setAddExtraToScope', [$params['scope']['add_extra']])
                ->addMethodCall('setAddBreadcrumbsToScope', [$params['scope']['add_breadcrumbs']]);
        }
    }
}