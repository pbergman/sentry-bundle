<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Monolog;

use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use Sentry\Breadcrumb;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

class SentryHandler extends AbstractHandler
{
    private $hub;

    public function __construct(HubInterface $hub, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->hub = $hub;
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
        if (empty($records)) {
            return;
        }

        $records = array_filter(
            $records,
            static function($record) {
                return $this->handle($record);
            }
        );

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
            $scope->setExtra('monolog.channel', $record['channel']);
            $scope->setExtra('monolog.level', $record['level_name']);

            foreach ((array)($record['context']['extra'] ?? []) as $key => $value) {
                $scope->setExtra((string) $key, $value);
            }

            foreach ((array)($record['context']['tags'] ?? []) as $key => $value) {
                $scope->setTag((string)$key, $value);
            }

            foreach ($breadcrumbs as $breadcrumb) {
                $scope->addBreadcrumb($this->toBreadcrumb($breadcrumb));
            }

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

    private function getBreadcrumbLevelFromLevel(int $level): string
    {
        switch ($level) {
            case Logger::DEBUG:
                return Breadcrumb::LEVEL_DEBUG;
            case Logger::INFO:
            case Logger::NOTICE:
                return Breadcrumb::LEVEL_INFO;
            case Logger::WARNING:
                return Breadcrumb::LEVEL_WARNING;
            case Logger::ERROR:
                return Breadcrumb::LEVEL_ERROR;
            default:
                return Breadcrumb::LEVEL_FATAL;
        }
    }

    private function toBreadcrumb(array $record): Breadcrumb
    {
        return Breadcrumb::fromArray([
            'level'     => $this->getBreadcrumbLevelFromLevel($record['level']),
            'type'      => ($record['level'] >= Logger::ERROR ? Breadcrumb::TYPE_ERROR: Breadcrumb::TYPE_DEFAULT),
            'category'  => ($record['channel'] ?? 'N/A'),
            'message'   => $record['message'],
            'data'      => (!empty($record['context']) ? $record['context'] : []),
            'timestamp' => (float)$record['datetime']->format('U'),
        ]);
    }
}
