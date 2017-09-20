<?php

namespace Tests;

use Ultraleet\CurrencyRates\CurrencyRates;
use Tests\Fixtures\TestProviderStub;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class CurrencyRatesTest extends PHPUnit_Framework_TestCase
{
    protected $factory;

    protected function setUp()
    {
        $this->factory = new CurrencyRates;
    }

    protected function tearDown()
    {
        unset($this->factory);
    }

    protected function extend()
    {
        $this->factory->extend('test', function () {
            return new TestProviderStub;
        });
    }

    public function testDriverWithoutParametersReturnsFixerProviderInstance()
    {
        $provider = $this->factory->driver();

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Providers\FixerProvider', $provider);
    }

    public function testDriverDummyReturnsDummyProviderInstance()
    {
        $provider = $this->factory->driver('dummy');

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Providers\DummyProvider', $provider);
    }

    public function testDriverYahooReturnsYahooProviderInstance()
    {
        $provider = $this->factory->driver('yahoo');

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Providers\YahooProvider', $provider);
    }

    public function testCallingSameDriverTwiceReturnsSameObject()
    {
        $provider1 = $this->factory->driver('dummy');
        $provider2 = $this->factory->driver('dummy');

        $this->assertSame($provider1, $provider2);
    }

    public function testExtendRegistersCustomDriverThatCanBeCreatedWithDriver()
    {
        $this->extend();
        $provider = $this->factory->driver('test');

        $this->assertInstanceOf('Tests\Fixtures\TestProviderStub', $provider);
        $this->assertInstanceOf('Ultraleet\CurrencyRates\AbstractProvider', $provider);
    }

    public function testSetDefaultDriver()
    {
        $this->extend();
        $provider = $this->factory->setDefaultDriver('test')->driver();

        $this->assertInstanceOf('Tests\Fixtures\TestProviderStub', $provider);
    }

    public function testCallDefaultDriverMethodsDirectly()
    {
        $this->extend();
        $result = $this->factory->setDefaultDriver('test')->latest();

        $this->assertEquals(1.1933, $result->getRate('USD'));
    }

    public function testGetDriversReturnsArrayOfCreatedDrivers()
    {
        $this->extend();
        $this->factory->setDefaultDriver('test');
        $this->factory->driver();
        $this->factory->driver('dummy');

        $this->assertEquals(['test', 'dummy'], array_keys($this->factory->getDrivers()));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Driver [foo] not supported.
     */
    public function testUnsupportedDriver()
    {
        $this->factory->driver('foo');
    }
}
