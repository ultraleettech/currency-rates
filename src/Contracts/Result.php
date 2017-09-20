<?php

namespace Ultraleet\CurrencyRates\Contracts;

interface Result
{
    /**
     * Get the base currency.
     *
     * @return string
     */
    public function getBase();

    /**
     * Get the date of the rates.
     *
     * @return \DateTime
     */
    public function getDate();

    /**
     * Get all requested currency conversions.
     *
     * @return array
     */
    public function getConverted();

    /**
     * Set all requested currency conversions.
     *
     * @param array
     * @return void
     */
    public function setConverted($converted);

    /**
     * Get the rate for the given currency.
     * Must return null if currency is not found in the result.
     *
     * @param string $currency
     * @return float|null
     */
    public function getRate($currency);
}
