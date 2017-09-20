<?php

namespace Tests;

use Ultraleet\CurrencyRates\Result;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;
use DateTime;

class ResultTest extends PHPUnit_Framework_TestCase
{
    protected $base;
    protected $date;
    protected $rates;
    protected $result;

    public function setUp()
    {
        $this->base = 'EUR';
        $this->date = new DateTime('2017-09-12');
        $this->rates = [
            'USD' => 1.1933,
            'GBP' => 0.89878,
        ];

        $this->result = new Result($this->base, $this->date, $this->rates);
    }

    public function testGettersReturnConstructorArguments()
    {
        $this->assertEquals($this->base, $this->result->getBase());
        $this->assertEquals($this->date, $this->result->getDate());
        $this->assertEquals($this->rates, $this->result->getRates());
    }

    public function testGetRateReturnsCorrectValue()
    {
        $this->assertEquals(1.1933, $this->result->getRate('USD'));
        $this->assertEquals(0.89878, $this->result->getRate('GBP'));
    }

    public function testGetRateReturnsOneForBaseCurrency()
    {
        $this->assertEquals(1, $this->result->getRate('EUR'));
    }

    public function testGetRateReturnsNullIfNotInResults()
    {
        $this->assertNull($this->result->getRate('NOK'));
        $this->assertNull($this->result->getRate('foobar'));
    }

    public function testMagicGetter()
    {
        $this->assertEquals($this->base, $this->result->base);
        $this->assertEquals($this->date, $this->result->date);
        $this->assertEquals($this->rates, $this->result->rates);

        if (class_exists('PHPUnit_Framework_Error_Notice')) {
            $this->expectException('PHPUnit_Framework_Error_Notice');
        } else {
            $this->expectException('PHPUnit\Framework\Error\Notice');
        }

        $this->expectExceptionMessage('Undefined property:');
        $this->foo;
    }
}
