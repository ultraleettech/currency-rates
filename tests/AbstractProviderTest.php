<?php

namespace Tests;

use Ultraleet\CurrencyRates\Providers\DummyProvider;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class AbstractProviderTest extends PHPUnit_Framework_TestCase
{
    protected $driver;

    protected function setUp()
    {
        $this->driver = new DummyProvider;
    }

    protected function tearDown()
    {
        unset($this->driver);
    }

    public function testConfigureReturnsDriver()
    {
        $driver = $this->driver->configure([]);

        $this->assertSame($this->driver, $driver);
    }
}
