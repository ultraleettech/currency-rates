<?php

namespace Tests\Fixtures;

use Ultraleet\CurrencyRates\AbstractProvider;
use Ultraleet\CurrencyRates\Result;

class InvalidProviderStub extends AbstractProvider
{
    public function latest($base = 'EUR', $targets = [])
    {
        return $this->historical('');
    }

    public function historical($date, $base = 'EUR', $targets = [])
    {
        return 'NotResult';
    }
}
