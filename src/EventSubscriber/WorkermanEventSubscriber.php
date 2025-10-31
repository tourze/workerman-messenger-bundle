<?php

namespace Tourze\WorkermanMessengerBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

class WorkermanEventSubscriber
{
    private \Closure $workermanChecker;

    public function __construct(?\Closure $workermanChecker = null)
    {
        $this->workermanChecker = $workermanChecker ?? static fn (): bool => Worker::isRunning();
    }

    #[AsEventListener]
    public function increaseWorkermanTotalRequest(WorkerMessageHandledEvent $event): void
    {
        if (!$this->isWorkerman()) {
            return;
        }
        ++ConnectionInterface::$statistics['total_request'];
    }

    #[AsEventListener]
    public function increaseWorkermanSendFailCount(WorkerMessageFailedEvent $event): void
    {
        if (!$this->isWorkerman()) {
            return;
        }
        ++ConnectionInterface::$statistics['send_fail'];
    }

    private function isWorkerman(): bool
    {
        return ($this->workermanChecker)();
    }
}
