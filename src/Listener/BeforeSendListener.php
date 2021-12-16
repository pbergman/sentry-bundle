<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use PBergman\Bundle\SentryBundle\Events;
use PBergman\Bundle\SentryBundle\Events\BeforeSendEvent;
use Sentry\Event;
use Sentry\EventHint;

class BeforeSendListener extends AbstractEventDispatcherBridge
{
    public function __invoke(Event $event, ?EventHint $hint): ?Event
    {
        return $this->dispatch(new BeforeSendEvent($event, $hint), Events::EVENT_BEFORE_SEND)->getEvent();
    }
}