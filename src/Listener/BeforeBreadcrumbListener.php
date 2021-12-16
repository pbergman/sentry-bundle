<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use PBergman\Bundle\SentryBundle\Events;
use PBergman\Bundle\SentryBundle\Events\BeforeBreadcrumbEvent;
use Sentry\Breadcrumb;

class BeforeBreadcrumbListener extends AbstractEventDispatcherBridge
{
    public function __invoke(Breadcrumb $breadcrumb): ?Breadcrumb
    {
        return $this->dispatch(new BeforeBreadcrumbEvent($breadcrumb), Events::EVENT_BEFORE_BREADCRUMB)->getBreadcrumb();
    }
}