<?php

namespace Tests\Providers;

use Ultraleet\CurrencyRates\Providers\DummyProvider;
use DateTime;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class DummyProviderTest extends PHPUnit_Framework_TestCase
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

    public function testLatestReturnsValidResult()
    {
        $result = $this->driver->latest();

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
    }

    public function testLatestWithArgumentsReturnsValidResult()
    {
        $result = $this->driver->latest('EUR', ['USD', 'GBP']);

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid base currency specified
     */
    public function testLatestWithUnsupportedBaseCurrencyThrowsException()
    {
        $result = $this->driver->latest('EEK', ['USD', 'GBP']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid target currency specified
     */
    public function testLatestWithUnsupportedTargetCurrencyThrowsException()
    {
        $result = $this->driver->latest('EUR', ['USD', 'EEK']);
    }

    public function testHistoricalReturnsValidResult()
    {
        $result = $this->driver->historical(new DateTime());

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
    }

    public function testHistoricalWithArgumentsReturnsValidResult()
    {
        $result = $this->driver->historical(new DateTime(), 'EUR', ['USD', 'GBP']);

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid base currency specified
     */
    public function testHistoricalWithUnsupportedBaseCurrencyThrowsException()
    {
        $result = $this->driver->historical(new DateTime(), 'EEK', ['USD', 'GBP']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid target currency specified
     */
    public function testHistoricalWithUnsupportedTargetCurrencyThrowsException()
    {
        $result = $this->driver->historical(new DateTime(), 'EUR', ['USD', 'EEK']);
    }
}
