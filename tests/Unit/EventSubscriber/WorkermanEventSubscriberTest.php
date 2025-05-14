<?php

namespace Workerman {
    class Worker
    {
        private static bool $isRunning = false;

        public static function isRunning(): bool
        {
            return self::$isRunning;
        }

        public static function setIsRunning(bool $isRunning): void
        {
            self::$isRunning = $isRunning;
        }
    }
}

namespace Workerman\Connection {
    class ConnectionInterface
    {
        public static array $statistics = [
            'total_request' => 0,
            'send_fail' => 0,
        ];

        public static function resetStatistics(): void
        {
            self::$statistics = [
                'total_request' => 0,
                'send_fail' => 0,
            ];
        }
    }
}

namespace Tourze\WorkermanMessengerBundle\Tests\Unit\EventSubscriber {

    use PHPUnit\Framework\TestCase;
    use Symfony\Component\Messenger\Envelope;
    use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
    use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
    use Tourze\WorkermanMessengerBundle\EventSubscriber\WorkermanEventSubscriber;
    use Workerman\Connection\ConnectionInterface;
    use Workerman\Worker;

    /**
     * @runTestsInSeparateProcesses
     */
    class WorkermanEventSubscriberTest extends TestCase
    {
        private WorkermanEventSubscriber $subscriber;
        private Envelope $envelope;

        protected function setUp(): void
        {
            ConnectionInterface::resetStatistics();
            Worker::setIsRunning(false);
            $this->subscriber = new WorkermanEventSubscriber();
            $this->envelope = new Envelope(new \stdClass());
        }

        /**
         * 创建一个新的WorkermanEventSubscriber实例，但使用指定的isWorkerman()返回值
         */
        private function createSubscriberWithIsWorkerman(bool $isWorkerman): WorkermanEventSubscriber
        {
            // 先设置Worker的运行状态
            Worker::setIsRunning($isWorkerman);
            
            // 创建新的实例
            return new WorkermanEventSubscriber();
        }

        public function testIncreaseWorkermanTotalRequest_whenNotInWorkerman(): void
        {
            // 创建一个在非Workerman环境中的订阅者
            $subscriber = $this->createSubscriberWithIsWorkerman(false);
            
            // 创建一个消息已处理事件
            $event = new WorkerMessageHandledEvent($this->envelope, 'receiver');
            
            // 执行订阅方法
            $subscriber->increaseWorkermanTotalRequest($event);
            
            // 断言统计数未增加
            $this->assertEquals(0, ConnectionInterface::$statistics['total_request']);
        }
        
        public function testIncreaseWorkermanTotalRequest_whenInWorkerman(): void
        {
            // 创建一个在Workerman环境中的订阅者
            $subscriber = $this->createSubscriberWithIsWorkerman(true);
            
            // 创建一个消息已处理事件
            $event = new WorkerMessageHandledEvent($this->envelope, 'receiver');
            
            // 执行订阅方法
            $subscriber->increaseWorkermanTotalRequest($event);
            
            // 断言统计数已增加
            $this->assertEquals(1, ConnectionInterface::$statistics['total_request']);
        }
        
        public function testIncreaseWorkermanSendFailCount_whenNotInWorkerman(): void
        {
            // 创建一个在非Workerman环境中的订阅者
            $subscriber = $this->createSubscriberWithIsWorkerman(false);
            
            // 创建一个消息处理失败事件
            $event = new WorkerMessageFailedEvent(
                $this->envelope,
                'receiver',
                new \Exception('Test exception')
            );
            
            // 执行订阅方法
            $subscriber->increaseWorkermanSendFailCount($event);
            
            // 断言统计数未增加
            $this->assertEquals(0, ConnectionInterface::$statistics['send_fail']);
        }
        
        public function testIncreaseWorkermanSendFailCount_whenInWorkerman(): void
        {
            // 创建一个在Workerman环境中的订阅者
            $subscriber = $this->createSubscriberWithIsWorkerman(true);
            
            // 创建一个消息处理失败事件
            $event = new WorkerMessageFailedEvent(
                $this->envelope,
                'receiver',
                new \Exception('Test exception')
            );
            
            // 执行订阅方法
            $subscriber->increaseWorkermanSendFailCount($event);
            
            // 断言统计数已增加
            $this->assertEquals(1, ConnectionInterface::$statistics['send_fail']);
        }
    }
} 