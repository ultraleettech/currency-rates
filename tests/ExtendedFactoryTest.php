<?php

namespace Tests;

use Tests\Fixtures\ExtendedFactoryStub;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class ExtendedFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $factory;
    protected $properties;

    protected function setUp()
    {
        $this->factory = new ExtendedFactoryStub;
    }

    protected function tearDown()
    {
        unset($this->factory);
    }

    public function testDriverTestReturnsTestProvider()
    {
        $provider = $this->factory->driver('test');

        $this->assertInstanceOf('Tests\Fixtures\TestProviderStub', $provider);
    }

    public function testOverrideDefaultProvider()
    {
        $provider = $this->factory->driver();
        $result = $this->factory->latest();

        $this->assertInstanceOf('Tests\Fixtures\TestProviderStub', $provider);
        $this->assertEquals(1.1933, $result->getRate('USD'));
    }
}
