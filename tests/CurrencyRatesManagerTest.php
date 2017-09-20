<?php

namespace Tests;

use Ultraleet\CurrencyRates\CurrencyRatesManager;

class CurrencyRatesManagerTest extends CurrencyRatesTest
{
    // Since CurrencyRatesManager simply proxies the functionality of
    // CurrencyRates, we can duplicate the tests for that class and simply
    // override the factory setup here.
    protected function setUp()
    {
        $this->factory = new CurrencyRatesManager(new \stdClass);
    }

    public function testGetDefaultDriver()
    {
        $provider = $this->factory->getDefaultDriver();

        $this->assertEquals('fixer', $provider);
    }
}
