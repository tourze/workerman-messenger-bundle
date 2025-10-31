# Workerman Messenger Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/your-org/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/your-org/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony Bundle that integrates Workerman with Symfony Messenger, providing seamless message processing statistics and event handling in high-performance Workerman environments.

## Installation

```bash
composer require tourze/workerman-messenger-bundle
```

## Quick Start

1. **Enable the Bundle**

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    Tourze\WorkermanMessengerBundle\WorkermanMessengerBundle::class => ['all' => true],
];
```

2. **Configure Symfony Messenger** (if not already configured)

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\YourMessage': async
```

3. **Run with Workerman**

```php
<?php
// workerman.php

use Workerman\Worker;
use App\Kernel;

$kernel = new Kernel('prod', false);
$kernel->boot();

$worker = new Worker();
$worker->onMessage = function($connection, $data) use ($kernel) {
    // Your message processing logic here
    // Statistics will be automatically tracked by the bundle
};

Worker::runAll();
```

## Features

- **Automatic Statistics Tracking**: Automatically increments Workerman statistics when Symfony Messenger processes messages
- **Environment Detection**: Only activates when running in Workerman environment
- **Zero Configuration**: Works out of the box with sensible defaults
- **Event-Driven Architecture**: Uses Symfony Event Dispatcher for loose coupling

## How It Works

The bundle provides an event subscriber that listens to Symfony Messenger events:

- `WorkerMessageHandledEvent`: Increments `total_request` counter
- `WorkerMessageFailedEvent`: Increments `send_fail` counter

The statistics are only updated when running in a Workerman environment, making it safe to use in both traditional web and Workerman contexts.

## Code Example

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
        // Dispatch message - statistics will be automatically tracked
        $this->bus->dispatch(new ProcessDataMessage($data));
    }
}
```

## Testing

```bash
# Run tests
./vendor/bin/phpunit packages/workerman-messenger-bundle/tests

# Run static analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/workerman-messenger-bundle
```

## Requirements

- PHP ^8.3
- Symfony ^7.3
- Workerman ^5.1

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).