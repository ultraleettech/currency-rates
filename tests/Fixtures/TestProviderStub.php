<?php

namespace Tests\Fixtures;

use Ultraleet\CurrencyRates\AbstractProvider;
use Ultraleet\CurrencyRates\Result;
use DateTime;

class TestProviderStub extends AbstractProvider
{
    public function latest($base = 'EUR', $targets = [])
    {
        return $this->historical(new DateTime('today'), $base, $targets);
    }

    public function historical($date, $base = 'EUR', $targets = [])
    {
        $rates = [];
        foreach ($targets as $currency) {
            $rates[$currency] = 1.337;
        }
        $rates['USD'] = 1.1933;

        return new Result($base, $date, $rates);
    }
}
