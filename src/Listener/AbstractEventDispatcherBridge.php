<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractEventDispatcherBridge
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    protected function dispatch(Event $event, string $name): Event
    {
        return $this->dispatcher->dispatch($event, $name);
    }
}