<?php

namespace Tests\Fixtures;

use Ultraleet\CurrencyRates\CurrencyRates;

class ExtendedFactoryStub extends CurrencyRates
{
    /**
     * The name of the default driver.
     *
     * @var string
     */
    protected $defaultDriver = 'test';

    /**
     * Create an instance of the specified driver.
     *
     * @return \Ultraleet\CurrencyRates\AbstractProvider
     */
    protected function createTestDriver()
    {
        return new TestProviderStub();
    }

}
