<?php

namespace Ultraleet\CurrencyRates;

use Ultraleet\CurrencyRates\Contracts\Result as ResultContract;
use DateTime;

class Result implements ResultContract
{
    /**
     * The Base currency the result was returned in
     * @var string
     */
    protected $base;

    /**
     * The date the result was generated for
     * @var DateTime
     */

    protected $date;

    /**
     * All of the rates returned
     * @var array
     */
    protected $rates;

    /**
     * Result constructor.
     *
     * @param string $base
     * @param DateTime $date
     * @param array $rates
     */
    public function __construct($base, DateTime $date, $rates)
    {
        $this->base = $base;
        $this->date = $date;
        $this->rates = $rates;
    }

    /**
     * Get the base currency.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Get the date of the rates.
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get the all requested currency rates.
     *
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * Get an individual rate by Currency code.
     * Will return null if currency is not found in the result.
     *
     * @param string $code
     * @return float|null
     */
    public function getRate($code)
    {
        // the result won't have the base code in it,
        // because that would always be 1. But to make
        // dynamic code easier this prevents null if
        // the base code is asked for
        if ($code == $this->getBase()) {
            return 1.0;
        }

        if (isset($this->rates[$code])) {
            return $this->rates[$code];
        }

        return null;
    }

    /*
     * Magic getter function for getting property values.
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        trigger_error('Undefined property: ' . get_class() . '::$' . $name, E_USER_NOTICE);
        return null;
    }
}
