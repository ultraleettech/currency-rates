<?php

namespace Ultraleet\CurrencyRates;

use Illuminate\Http\Request;
use Ultraleet\CurrencyRates\Contracts\Provider as ProviderContract;
use DateTime;

abstract class AbstractProvider implements ProviderContract
{
    protected $config = [];
    protected $base = 'EUR';
    protected $targets = [];
    protected $date = null;

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
     * Query the API.
     *
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function get()
    {
        if ($this->date) {
            return $this->historical($this->date, $this->base, $this->targets);
        } else {
            return $this->latest($this->base, $this->targets);
        }
    }
}
