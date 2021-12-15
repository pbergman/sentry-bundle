<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Events;

use Sentry\EventHint;
use Sentry\Event;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

final class BeforeSendEvent extends BaseEvent
{
    private $event;
    private $hint;

    public function __construct(Event $event, ?EventHint $hint)
    {
        $this->event = $event;
        $this->hint  = $hint;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function getHint(): ?EventHint
    {
        return $this->hint;
    }

    public function setEvent(?Event $event): BeforeSendEvent
    {
        $this->event = $event;

        return $this;
    }

}