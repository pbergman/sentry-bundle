<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use PBergman\Bundle\SentryBundle\Events\BeforeSendEvent;

class ExceptionExcludeListener
{
    private $exclude;

    public function __construct(array $exclude)
    {
        $this->exclude = $exclude;
    }

    public function __invoke(BeforeSendEvent $event)
    {
        if (null !== $hint = $event->getHint()) {
            foreach ($this->exclude as $exclude) {
                if (is_a($hint->exception, $exclude, true)) {
                    $event->setEvent(null);
                    $event->stopPropagation();
                    return;
                }
            }
        }
    }
}
