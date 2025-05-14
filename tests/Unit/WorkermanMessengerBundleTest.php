<?php

namespace Tourze\WorkermanMessengerBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\WorkermanMessengerBundle\WorkermanMessengerBundle;

class WorkermanMessengerBundleTest extends TestCase
{
    public function testBundleInheritance(): void
    {
        $bundle = new WorkermanMessengerBundle();
        
        // 验证Bundle继承自Symfony的Bundle基类
        $this->assertInstanceOf(Bundle::class, $bundle);
    }
    
    public function testBundleName(): void
    {
        $bundle = new WorkermanMessengerBundle();
        
        // 获取Bundle的名称
        $name = $bundle->getName();
        
        // 验证名称是否正确
        $this->assertEquals('WorkermanMessengerBundle', $name);
    }
} 