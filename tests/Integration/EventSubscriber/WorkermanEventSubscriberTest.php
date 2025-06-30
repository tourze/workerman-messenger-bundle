<?php

namespace Tourze\WorkermanMessengerBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Tourze\WorkermanMessengerBundle\EventSubscriber\WorkermanEventSubscriber;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

class WorkermanEventSubscriberTest extends TestCase
{
    private WorkermanEventSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriber = new WorkermanEventSubscriber();
        
        // Reset statistics
        ConnectionInterface::$statistics = [
            'total_request' => 0,
            'send_fail' => 0,
            'throw_exception' => 0,
            'total_recv' => 0,
            'total_send' => 0,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Reset statistics
        ConnectionInterface::$statistics = [
            'total_request' => 0,
            'send_fail' => 0,
            'throw_exception' => 0,
            'total_recv' => 0,
            'total_send' => 0,
        ];
    }

    public function testIncreaseWorkermanTotalRequestWhenNotInWorkerman(): void
    {
        $envelope = new Envelope(new \stdClass());
        $event = new WorkerMessageHandledEvent($envelope, 'test-receiver');

        $initialCount = ConnectionInterface::$statistics['total_request'];
        $this->subscriber->increaseWorkermanTotalRequest($event);
        
        // 当不在Workerman环境中时，统计值不应该增加
        $this->assertEquals($initialCount, ConnectionInterface::$statistics['total_request']);
    }

    public function testIncreaseWorkermanSendFailCountWhenNotInWorkerman(): void
    {
        $envelope = new Envelope(new \stdClass());
        $event = new WorkerMessageFailedEvent($envelope, 'test-receiver', new \Exception('Test exception'));

        $initialCount = ConnectionInterface::$statistics['send_fail'];
        $this->subscriber->increaseWorkermanSendFailCount($event);
        
        // 当不在Workerman环境中时，统计值不应该增加
        $this->assertEquals($initialCount, ConnectionInterface::$statistics['send_fail']);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $subscriber = new WorkermanEventSubscriber();
        
        // 验证对象被正确创建
        $this->assertInstanceOf(WorkermanEventSubscriber::class, $subscriber);
    }

    public function testEventListenersAreProperlyConfigured(): void
    {
        // 使用反射来检查事件监听器是否正确配置
        $reflection = new \ReflectionClass(WorkermanEventSubscriber::class);
        
        // 检查 increaseWorkermanTotalRequest 方法
        $increaseRequestMethod = $reflection->getMethod('increaseWorkermanTotalRequest');
        $attributes = $increaseRequestMethod->getAttributes(\Symfony\Component\EventDispatcher\Attribute\AsEventListener::class);
        $this->assertNotEmpty($attributes, 'increaseWorkermanTotalRequest should have AsEventListener attribute');
        
        // 检查 increaseWorkermanSendFailCount 方法
        $increaseFailMethod = $reflection->getMethod('increaseWorkermanSendFailCount');
        $attributes = $increaseFailMethod->getAttributes(\Symfony\Component\EventDispatcher\Attribute\AsEventListener::class);
        $this->assertNotEmpty($attributes, 'increaseWorkermanSendFailCount should have AsEventListener attribute');
    }
}