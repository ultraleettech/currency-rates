<?php

namespace Ultraleet\CurrencyRates\Contracts;

interface Provider
{
    /**
     * Get latest currency exchange rates.
     *
     * @param  string  $base
     * @param  array   $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function latest($base = 'EUR', $targets = []);

    /**
     * Get historical currency exchange rates.
     *
     * @param  \DateTime $base
     * @param  string    $base
     * @param  array     $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function historical($date, $base = 'EUR', $targets = []);
}
