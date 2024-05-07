<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
     {
        $treeBuilder = new TreeBuilder('p_bergman_sentry');

        $root = $treeBuilder->getRootNode();
        $root
            ->children()
                ->arrayNode('excluded_exceptions')
                    ->info('Exception classes which will be excluded from sending to remote.')
                    ->beforeNormalization()
                        ->castToArray()
                    ->end()
                    ->scalarPrototype()->end()
                    ->defaultValue([
                        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
                        'Symfony\Component\Security\Core\Exception\AccessDeniedException',
                    ])
                ->end()
                ->arrayNode('scope')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('add_tags')
                            ->info('When true it will check for tags in the message context and add them to sentry event tags context.')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('add_extra')
                            ->info('When true it will check for extra`s in the message context and add them to sentry event extra context.')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('add_breadcrumbs')
                            ->info('When true it will build breadcrumbs when handle batch is called.')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('add_request')
                            ->info('When true it will add request context to scope.')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
