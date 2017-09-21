<?php

namespace Ultraleet\CurrencyRates;

use Ultraleet\CurrencyRates\Contracts\Provider as ProviderContract;
use Ultraleet\CurrencyRates\Contracts\Result as ResultContract;
use Ultraleet\CurrencyRates\Exceptions\UnexpectedValueException;
use DateTime;

abstract class AbstractProvider implements ProviderContract
{
    protected $config = [];
    protected $base = 'EUR';
    protected $targets = [];
    protected $date = null;
    protected $amount = 1;

    /**
     * Set the configuration array for this provider.
     *
     * @param array $config
     * @return self
     */
    public function configure($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the base currency.
     *
     * @param string $currency
     * @return self
     */
    public function base($currency)
    {
        $this->base = $currency;

        return $this;
    }

    /**
     * Set the target currencies.
     *
     * @param array|string $currencies
     * @return self
     */
    public function target($currencies)
    {
        if (!is_array($currencies)) {
            $currencies = [$currencies];
        }

        $this->targets = $currencies;

        return $this;
    }

    /**
     * Set the date.
     *
     * @param mixed $date Can be a string, a DateTime object, or null to unset.
     * @return self
     */
    public function date($date = null)
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }

        $this->date = $date;

        return $this;
    }

    /**
     * Set the amount of base currency to convert.
     *
     * @param float
     * @return self
     */
    public function amount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Query the API.
     *
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function get()
    {
        if ($this->date) {
            $result = $this->historical($this->date, $this->base, $this->targets);
        } else {
            $result = $this->latest($this->base, $this->targets);
        }

        // Ensure the result object is valid.
        if (!$result instanceof ResultContract) {
            throw new UnexpectedValueException('Invalid result type');
        }

        // Perform conversion if requested.
        if ($this->amount !== 1) {
            $converted = $result->rates;

            foreach ($converted as $key => $value) {
                $converted[$key] = round($this->amount * $value, 2);
            }

            // Attach converted values to results.
            $result->setConverted($converted);
        }

        return $result;
    }
}
