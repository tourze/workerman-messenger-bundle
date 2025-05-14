<?php

namespace Tourze\WorkermanMessengerBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\WorkermanMessengerBundle\DependencyInjection\WorkermanMessengerExtension;

class WorkermanMessengerExtensionTest extends TestCase
{
    private WorkermanMessengerExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WorkermanMessengerExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadRegistersServices(): void
    {
        // 手动注册资源目录
        $this->container->fileExists(dirname(__DIR__, 3) . '/src/Resources/config/services.yaml');
        
        // 加载扩展
        $this->extension->load([], $this->container);
        
        // 验证服务是否注册（使用服务ID）
        $this->assertTrue(
            $this->container->hasDefinition('Tourze\WorkermanMessengerBundle\EventSubscriber\WorkermanEventSubscriber'),
            '容器应该包含WorkermanEventSubscriber服务定义'
        );
        
        // 验证服务的自动配置和自动装配
        $definition = $this->container->getDefinition('Tourze\WorkermanMessengerBundle\EventSubscriber\WorkermanEventSubscriber');
        $this->assertTrue($definition->isAutoconfigured(), 'WorkermanEventSubscriber服务应该自动配置');
        $this->assertTrue($definition->isAutowired(), 'WorkermanEventSubscriber服务应该自动装配');
    }
    
    public function testServicesFileExists(): void
    {
        // 测试services.yaml文件是否存在
        $filePath = dirname(__DIR__, 3) . '/src/Resources/config/services.yaml';
        $this->assertFileExists($filePath, 'services.yaml文件应该存在');
        
        // 测试文件内容是否包含EventSubscriber服务配置
        $fileContent = file_get_contents($filePath);
        $this->assertStringContainsString('Tourze\WorkermanMessengerBundle\EventSubscriber\:', $fileContent);
    }
} 