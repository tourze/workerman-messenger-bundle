<?php

declare(strict_types=1);

namespace Tourze\WorkermanMessengerBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\WorkermanMessengerBundle\WorkermanMessengerBundle;

/**
 * @internal
 */
// Test for WorkermanMessengerBundle
#[CoversClass(WorkermanMessengerBundle::class)]
#[RunTestsInSeparateProcesses]
final class WorkermanMessengerBundleTest extends AbstractBundleTestCase
{
}
