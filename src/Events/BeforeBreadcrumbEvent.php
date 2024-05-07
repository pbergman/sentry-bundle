<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Events;

use Sentry\Breadcrumb;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

final class BeforeBreadcrumbEvent extends BaseEvent
{
    private Breadcrumb $breadcrumb;

    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function getBreadcrumb(): ?Breadcrumb
    {
        return $this->breadcrumb;
    }

    public function setBreadcrumb(?Breadcrumb $breadcrumb): BeforeBreadcrumbEvent
    {
        $this->breadcrumb = $breadcrumb;
        return $this;
    }
}