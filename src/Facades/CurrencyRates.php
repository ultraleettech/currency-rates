<?php

namespace Ultraleet\CurrencyRates\Facades;

use Illuminate\Support\Facades\Facade;
use Ultraleet\CurrencyRates\Contracts\Factory;

/**
 * @see \Ultraleet\CurrencyRates\CurrencyRatesManager
 */
class CurrencyRates extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
