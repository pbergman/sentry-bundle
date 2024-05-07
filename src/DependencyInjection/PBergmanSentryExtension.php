<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\DependencyInjection;

use PBergman\Bundle\SentryBundle\Events;
use PBergman\Bundle\SentryBundle\Listener\ExceptionExcludeListener;
use PBergman\Bundle\SentryBundle\Listener\ScopeRecordListener;
use PBergman\Bundle\SentryBundle\Listener\ScopeRequestListener;
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
            $container->removeDefinition(ExceptionExcludeListener::class);
        } else {
            $container
                ->getDefinition(ExceptionExcludeListener::class)
                ->setArgument(0, $config['excluded_exceptions']);
        }

        $mode = 0;

        if ($config['scope']['add_tags']) {
            $mode |= ScopeRecordListener::ADD_TAGS;
        }

        if ($config['scope']['add_extra']) {
            $mode |= ScopeRecordListener::ADD_EXTRA;
        }

        if ($config['scope']['add_breadcrumbs']) {
            $mode |= ScopeRecordListener::ADD_BREADCRUMBS;
        }

        if (0 === $mode) {
            $container->removeDefinition(ScopeRecordListener::class);
        } else {
            $container
                ->getDefinition(ScopeRecordListener::class)
                ->setArgument(0, $mode);
        }

        if (false === $config['scope']['add_request']) {
            $container->removeDefinition(ScopeRequestListener::class);
        }


//
//        <service id="PBergman\Bundle\SentryBundle\Monolog\Listener\RequestScopeListener" public="false">
//            <tag name="kernel.event_listener" event="pbergman.sentry.scope_provider" lazy="true"/>
//        </service>


        $container->setParameter('pbergman.sentry_handler.options', ['scope' => $config['scope']]);
    }
}
