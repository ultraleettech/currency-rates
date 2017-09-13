<?php

namespace Tests\Fixtures;

use Ultraleet\CurrencyRates\AbstractProvider;
use Ultraleet\CurrencyRates\Result;
use DateTime;

class TestProviderStub extends AbstractProvider
{
    public function latest($base = 'EUR', $targets = [])
    {
        return new Result($base, new DateTime, ['USD' => 1.1933]);
    }

    public function historical($date, $base = 'EUR', $targets = [])
    {
        return new Result($base, new DateTime($date), ['USD' => 1.1933]);
    }
}
