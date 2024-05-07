<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Events;

use Sentry\State\Scope;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

final class ScopeEvent extends BaseEvent
{
    private array $records;
    private Scope $scope;

    public function __construct(Scope $scope, array ...$record)
    {
        $this->scope   = $scope;
        $this->records = $record;
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function getScope(): Scope
    {
        return $this->scope;
    }
}