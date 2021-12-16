<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection;

use PBergman\Bundle\SentryBundle\Events;
use PBergman\Bundle\SentryBundle\Listener\ExceptionExcludeListener;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class PBergmanSentryExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__FILE__ , 2). '/Resources/config'));
        $loader->load('services.xml');
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (false === empty($config['excluded_exceptions'])) {
            $def = new Definition(ExceptionExcludeListener::class, [$config['excluded_exceptions']]);
            $def->setPublic(false);
            $def->addTag('kernel.event_listener', ['event' => Events::EVENT_BEFORE_SEND, 'lazy' => true]);
            $container->setDefinition(ExceptionExcludeListener::class, $def);
        }

        $container->setParameter('pbergman.sentry_handler.options', ['scope' => $config['scope']]);
    }
}
