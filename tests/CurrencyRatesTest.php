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

    public function testCallingSameDriverTwiceReturnsSameObject()
    {
        $provider1 = $this->factory->driver('dummy');
        $provider2 = $this->factory->driver('dummy');

        $this->assertSame($provider1, $provider2);
    }

    public function testExtendRegistersCustomDriverThatCanBeCreatedWithDriver()
    {
        $this->factory->extend('test', function () {
            return new TestProviderStub;
        });

        $provider = $this->factory->driver('test');

        $this->assertInstanceOf('Tests\Fixtures\TestProviderStub', $provider);
        $this->assertInstanceOf('Ultraleet\CurrencyRates\AbstractProvider', $provider);
    }
}
