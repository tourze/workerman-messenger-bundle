# Workerman Messenger Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/your-org/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/your-org/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

一个将 Workerman 与 Symfony Messenger 集成的 Symfony Bundle，在高性能的 Workerman 环境中提供无缝的消息处理统计和事件处理。

## 安装

```bash
composer require tourze/workerman-messenger-bundle
```

## 快速开始

1. **启用 Bundle**

将 bundle 添加到你的 `config/bundles.php`：

```php
<?php

return [
    // ... 其他 bundles
    Tourze\WorkermanMessengerBundle\WorkermanMessengerBundle::class => ['all' => true],
];
```

2. **配置 Symfony Messenger**（如果尚未配置）

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\YourMessage': async
```

3. **使用 Workerman 运行**

```php
<?php
// workerman.php

use Workerman\Worker;
use App\Kernel;

$kernel = new Kernel('prod', false);
$kernel->boot();

$worker = new Worker();
$worker->onMessage = function($connection, $data) use ($kernel) {
    // 你的消息处理逻辑
    // Bundle 会自动跟踪统计信息
};

Worker::runAll();
```

## 功能特性

- **自动统计追踪**：当 Symfony Messenger 处理消息时自动增加 Workerman 统计信息
- **环境检测**：仅在 Workerman 环境中运行时激活
- **零配置**：开箱即用，具有合理的默认设置
- **事件驱动架构**：使用 Symfony Event Dispatcher 实现松耦合

## 工作原理

Bundle 提供了一个事件订阅器，监听 Symfony Messenger 事件：

- `WorkerMessageHandledEvent`：增加 `total_request` 计数器
- `WorkerMessageFailedEvent`：增加 `send_fail` 计数器

统计信息仅在 Workerman 环境中运行时更新，使其在传统 Web 和 Workerman 上下文中都安全使用。

## 代码示例

```php
<?php

use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProcessDataMessage;

class DataProcessor
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function processData(array $data): void
    {
        // 分发消息 - 统计信息将被自动跟踪
        $this->bus->dispatch(new ProcessDataMessage($data));
    }
}
```

## 测试

```bash
# 运行测试
./vendor/bin/phpunit packages/workerman-messenger-bundle/tests

# 运行静态分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/workerman-messenger-bundle
```

## 系统要求

- PHP ^8.3
- Symfony ^7.3
- Workerman ^5.1

## 许可证

本包是根据 [MIT 许可证](LICENSE) 开源的软件。