<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Monolog;

use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use PBergman\Bundle\SentryBundle\Events;
use PBergman\Bundle\SentryBundle\Events\ScopeEvent;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SentryHandler extends AbstractHandler
{
    private HubInterface $hub;
    private EventDispatcherInterface $dispatcher;

    public function __construct(HubInterface $hub, EventDispatcherInterface $dispatcher, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->hub        = $hub;
        $this->dispatcher = $dispatcher;
    }

    public function handle(array $record)
    {
        if (false === $this->isHandling($record)) {
            return null;
        }

        $this->push($this->process($record));

        return false === $this->bubble;
    }

    private function process(array $record): ?array
    {
        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = $processor($record);
            }
        }

        $record['formatted'] = $this->getFormatter()->format($record);

        return $record;
    }

    public function handleBatch(array $records)
    {
        if ([] === $records = \array_filter($records, [$this, 'isHandling'])) {
            return;
        }

        $levels  = \array_column($records, 'level');
        $index   = \array_search(\max($levels), $levels);
        $records = \array_map([$this, 'process'], $records);

        $this->push($records[$index], $records);
    }

    protected function push(array $record, array $breadcrumbs = []): void
    {
        $event = Event::createEvent();
        $event->setLevel(self::getSeverityFromLevel($record['level']));
        $event->setMessage($record['message']);
        $event->setLogger(sprintf('monolog.%s', $record['channel']));
        $event->setTimestamp((float)$record['datetime']->format('U'));

        $hint = new EventHint();

        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
            $hint->exception = $record['context']['exception'];
        }

        $this->hub->withScope(function (Scope $scope) use ($record, $event, $hint, $breadcrumbs): void {
            $this->dispatcher->dispatch(new ScopeEvent($scope, ...[$record, ...$breadcrumbs]), Events::EVENT_SCOPE_PROVIDER);
            $this->hub->captureEvent($event, $hint);
        });
    }

    private static function getSeverityFromLevel(int $level): Severity
    {
        switch ($level) {
            case Logger::DEBUG:
                return Severity::debug();
            case Logger::WARNING:
                return Severity::warning();
            case Logger::ERROR:
                return Severity::error();
            case Logger::CRITICAL:
            case Logger::ALERT:
            case Logger::EMERGENCY:
                return Severity::fatal();
            case Logger::INFO:
            case Logger::NOTICE:
            default:
                return Severity::info();
        }
    }
}
