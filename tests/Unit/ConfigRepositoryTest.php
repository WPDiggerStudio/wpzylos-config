<?php

declare(strict_types=1);

namespace WPZylos\Framework\Config\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Config\ConfigRepository;

/**
 * Tests for ConfigRepository class.
 */
class ConfigRepositoryTest extends TestCase
{
    private ConfigRepository $config;

    protected function setUp(): void
    {
        $this->config = new ConfigRepository();
    }

    public function testSetAndGet(): void
    {
        $this->config->set('app.name', 'TestApp');

        $this->assertSame('TestApp', $this->config->get('app.name'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        $result = $this->config->get('missing.key', 'default');

        $this->assertSame('default', $result);
    }

    public function testStringReturnsString(): void
    {
        $this->config->set('app.name', 'TestApp');

        $this->assertSame('TestApp', $this->config->string('app.name'));
    }

    public function testIntReturnsInteger(): void
    {
        $this->config->set('app.port', 8080);

        $this->assertSame(8080, $this->config->int('app.port'));
    }

    public function testBoolReturnsBool(): void
    {
        $this->config->set('app.debug', true);

        $this->assertTrue($this->config->bool('app.debug'));
    }

    public function testArrayReturnsArray(): void
    {
        $this->config->set('app.drivers', ['file', 'redis']);

        $this->assertSame(['file', 'redis'], $this->config->array('app.drivers'));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        $this->config->set('app.name', 'TestApp');

        $this->assertTrue($this->config->has('app.name'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $this->assertFalse($this->config->has('missing.key'));
    }

    public function testNestedDotNotation(): void
    {
        $this->config->set('database.connections.mysql.host', 'localhost');

        $this->assertSame('localhost', $this->config->get('database.connections.mysql.host'));
    }

    public function testAllReturnsAllConfig(): void
    {
        $this->config->set('a', 1);
        $this->config->set('b', 2);

        $all = $this->config->all();

        $this->assertArrayHasKey('a', $all);
        $this->assertArrayHasKey('b', $all);
    }
}
