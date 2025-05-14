<?php

namespace Tourze\WorkermanMessengerBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Tourze\WorkermanMessengerBundle\EventSubscriber\WorkermanEventSubscriber;

class WorkermanMessengerIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testServiceWiring(): void
    {
        $container = self::getContainer();
        
        // 测试WorkermanEventSubscriber服务是否正确注册
        $this->assertTrue($container->has(WorkermanEventSubscriber::class));
        $subscriber = $container->get(WorkermanEventSubscriber::class);
        $this->assertInstanceOf(WorkermanEventSubscriber::class, $subscriber);
    }

    public function testEventSubscriberRegistration(): void
    {
        $container = self::getContainer();
        $dispatcher = $container->get(EventDispatcherInterface::class);
        
        // 获取所有为WorkerMessageHandledEvent注册的监听器
        $handledListeners = $dispatcher->getListeners(WorkerMessageHandledEvent::class);
        $hasHandledSubscriber = false;
        
        foreach ($handledListeners as $listener) {
            if (is_array($listener) && $listener[0] instanceof WorkermanEventSubscriber) {
                $hasHandledSubscriber = true;
                break;
            }
        }
        
        $this->assertTrue($hasHandledSubscriber, 'WorkermanEventSubscriber应该订阅WorkerMessageHandledEvent事件');
        
        // 获取所有为WorkerMessageFailedEvent注册的监听器
        $failedListeners = $dispatcher->getListeners(WorkerMessageFailedEvent::class);
        $hasFailedSubscriber = false;
        
        foreach ($failedListeners as $listener) {
            if (is_array($listener) && $listener[0] instanceof WorkermanEventSubscriber) {
                $hasFailedSubscriber = true;
                break;
            }
        }
        
        $this->assertTrue($hasFailedSubscriber, 'WorkermanEventSubscriber应该订阅WorkerMessageFailedEvent事件');
    }
} 