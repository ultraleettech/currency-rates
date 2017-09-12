<?php

namespace Ultraleet\CurrencyRates\Contracts;

interface Factory
{
    /**
     * Get a currency rates API provider implementation.
     *
     * @param  string  $driver
     * @return \Ultraleet\CurrencyRates\Contracts\Provider
     */
    public function driver($driver = null);
}
