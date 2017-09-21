<?php

namespace Tests;

use Tests\Fixtures\TestProviderStub;
use Tests\Fixtures\InvalidProviderStub;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;
use DateTime;

class AbstractProviderTest extends PHPUnit_Framework_TestCase
{
    protected $driver;

    protected function setUp()
    {
        $this->driver = new TestProviderStub;
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

    public function testGetReturnsLatestResults()
    {
        $resultGet = $this->driver->get();
        $resultLatest = $this->driver->latest();

        $this->assertEquals($resultGet, $resultLatest);
    }

    public function testFluentDate()
    {
        $resultLatest = $this->driver->latest();
        $resultHistorical = $this->driver->historical(new DateTime('2001-01-03'));

        $resultDateString = $this->driver->date('2001-01-03')->get();
        $resultDateDT = $this->driver->date(new DateTime('2001-01-03'))->get();
        $resultDateNull = $this->driver->date()->get();

        $this->assertEquals($resultHistorical, $resultDateString);
        $this->assertEquals($resultHistorical, $resultDateDT);
        $this->assertEquals($resultLatest, $resultDateNull);
    }

    public function testFluentBaseAndTarget()
    {
        $resultLatestBase = $this->driver->latest('EEK');
        $resultLatestTarget = $this->driver->latest('EUR', ['EEK']);
        $resultLatestTargets = $this->driver->latest('EUR', ['EEK', 'USD']);
        $resultLatestBoth = $this->driver->latest('EEK', ['EUR']);

        $resultBase = $this->driver->base('EEK')->get();
        $resultTarget = $this->driver->base('EUR')->target('EEK')->get();           // string target
        $resultTargets = $this->driver->base('EUR')->target(['EEK', 'USD'])->get(); // targets array
        $resultBoth = $this->driver->base('EEK')->target(['EUR'])->get();

        $this->assertEquals($resultLatestBase, $resultBase);
        $this->assertEquals($resultLatestTarget, $resultTarget);
        $this->assertEquals($resultLatestTargets, $resultTargets);
        $this->assertEquals($resultLatestBoth, $resultBoth);
    }

    public function testConvert()
    {
        $result = $this->driver->target(['GBP', 'USD'])->amount(2)->get();
        $expected = [
            'GBP' => 2.67,
            'USD' => 2.39,
        ];

        $this->assertEquals($result->converted, $expected);
    }

    /**
     * @expectedException \Ultraleet\CurrencyRates\Exceptions\UnexpectedValueException
     * @expectedExceptionMessage Invalid result type
     */
    public function testInvalidProviderImplementation()
    {
        $driver = new InvalidProviderStub;

        $driver->get();
    }
}
