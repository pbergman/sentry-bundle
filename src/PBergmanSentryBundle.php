<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PBergmanSentryBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DependencyInjection\CompilerPass\SentryHandlerPass());
        $container->addCompilerPass(new DependencyInjection\CompilerPass\SentryOptionsPass());
    }
}