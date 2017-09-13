<?php

namespace Tests\Fixtures;

use Ultraleet\CurrencyRates\AbstractProvider;
use Ultraleet\CurrencyRates\Result;
use DateTime;

class TestProviderStub extends AbstractProvider
{
    public function latest($base = 'EUR', $targets = [])
    {

    }

    public function historical($date, $base = 'EUR', $targets = [])
    {

    }
}
