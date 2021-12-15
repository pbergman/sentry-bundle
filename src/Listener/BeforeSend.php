<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use PBergman\Bundle\SentryBundle\Events;
use PBergman\Bundle\SentryBundle\Events\BeforeSendEvent;
use Sentry\Event;
use Sentry\EventHint;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BeforeSend
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Event $event, ?EventHint $hint): ?Event
    {
        return $this->dispatcher->dispatch(new BeforeSendEvent($event, $hint), Events::EVENT_BEFORE_SEND)->getEvent();
    }
}