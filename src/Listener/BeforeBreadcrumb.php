<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use PBergman\Bundle\SentryBundle\Events\BeforeBreadcrumbEvent;
use Sentry\Breadcrumb;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BeforeBreadcrumb
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Breadcrumb $breadcrumb): ?Breadcrumb
    {
        return $this->dispatcher->dispatch(new BeforeBreadcrumbEvent($breadcrumb))->getBreadcrumb();
    }
}