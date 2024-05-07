<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use Monolog\Logger;
use PBergman\Bundle\SentryBundle\Events\ScopeEvent;
use Sentry\Breadcrumb;

class ScopeRecordListener
{
    public const ADD_TAGS        = 0x01;
    public const ADD_EXTRA       = 0x02;
    public const ADD_BREADCRUMBS = 0x04;
    public const ADD_ALL         = self::ADD_TAGS|self::ADD_EXTRA|self::ADD_BREADCRUMBS;
    private int $mode;

    public function __construct(int $mode = self::ADD_ALL)
    {
        $this->mode = $mode;
    }

    public function __invoke(ScopeEvent $event)
    {
        $records = $event->getRecords();
        $record  = \array_shift($records);
        $scope   = $event->getScope();

        $scope->setExtra('monolog.channel', $record['channel']);
        $scope->setExtra('monolog.level', $record['level_name']);

        if (self::ADD_EXTRA === ($this->mode & self::ADD_EXTRA)) {
            foreach ((array)($record['context']['extra'] ?? []) as $key => $value) {
                $scope->setExtra((string) $key, $value);
            }
        }

        if (self::ADD_TAGS === ($this->mode & self::ADD_TAGS)) {
            foreach ((array)($record['context']['tags'] ?? []) as $key => $value) {
                $scope->setTag((string)$key, $value);
            }
        }

        if (self::ADD_BREADCRUMBS === ($this->mode & self::ADD_BREADCRUMBS)) {
            foreach ($records as $record) {
                $scope->addBreadcrumb($this->toBreadcrumb($record));
            }
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

    public function setMode(int $mode): void
    {
        $this->mode = $mode;
    }

    public function getMode(): int
    {
        return $this->mode;
    }
}