<?php

namespace Ultraleet\CurrencyRates\Contracts;

interface Provider
{
    /**
     * Get latest currency exchange rates.
     *
     * @param  string  $base
     * @param  array   $targets
     *
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function latest($base = 'EUR', $targets = []);

    /**
     * Get historical currency exchange rates.
     *
     * @param  \DateTime $date
     * @param  string    $base
     * @param  array     $targets
     *
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function historical($date, $base = 'EUR', $targets = []);

    /**
     * Set the configuration array for this provider.
     *
     * @param array $config
     * @return self
     */
    public function configure($config);

    /**
     * Set the base currency.
     *
     * @param string $currency
     * @return self
     */
    public function base($currency);

    /**
     * Set the target currencies.
     *
     * @param array|string $currencies
     * @return self
     */
    public function target($currencies);

    /**
     * Set the date.
     *
     * @param mixed $date Can be a string, a DateTime object, or null to unset.
     * @return self
     */
    public function date($date);

    /**
     * Query the API.
     *
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function get();
}
