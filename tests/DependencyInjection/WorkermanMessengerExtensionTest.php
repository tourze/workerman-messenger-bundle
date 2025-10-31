<?php

namespace Tourze\WorkermanMessengerBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\WorkermanMessengerBundle\DependencyInjection\WorkermanMessengerExtension;

/**
 * @internal
 */
#[CoversClass(WorkermanMessengerExtension::class)]
final class WorkermanMessengerExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testServicesFileExists(): void
    {
        // 测试services.yaml文件是否存在
        $filePath = dirname(__DIR__, 2) . '/src/Resources/config/services.yaml';
        $this->assertFileExists($filePath, 'services.yaml文件应该存在');

        // 测试文件内容是否包含EventSubscriber服务配置
        $fileContent = file_get_contents($filePath);
        $this->assertNotFalse($fileContent, '无法读取services.yaml文件内容');
        $this->assertStringContainsString('Tourze\WorkermanMessengerBundle\EventSubscriber\:', $fileContent);
    }
}
