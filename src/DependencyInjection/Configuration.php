<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
     {
        $treeBuilder = new TreeBuilder('pbergman_sentry');
        $rootNode = $treeBuilder->getRootNode();
        return $treeBuilder;
    }
}
