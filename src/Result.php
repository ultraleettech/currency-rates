<?php

namespace Ultraleet\CurrencyRates;

use Ultraleet\CurrencyRates\Contracts\Result as ResultContract;
use DateTime;

/**
 * Encapsulates results from an API call.
 *
 * @property-read string    $base      The base currency
 * @property-read \DateTime $date      The date of the resulting rates
 * @property-read array     $rates     An array of currency-rate pairs
 * @property-read array     $converted Converted rates (currency-amount pairs)
 */
class Result implements ResultContract
{
    /**
     * The base currency the result was returned in.
     * @var string
     */
    protected $base;

    /**
     * The date the result was generated for.
     * @var \DateTime
     */

    protected $date;

    /**
     * All of the rates returned.
     * @var array
     */
    protected $rates;

    /**
     * All of the converted amounts.
     * @var array
     */
    protected $converted;

    /**
     * Result constructor.
     *
     * @param string $base
     * @param \DateTime $date
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
     * Get all requested currency rates.
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
        // Return 1 for base currency
        if ($code == $this->getBase()) {
            return 1.0;
        }

        if (isset($this->rates[$code])) {
            return $this->rates[$code];
        }

        return null;
    }

    /**
     * Get all requested currency conversions.
     *
     * @return array
     */
    public function getConverted()
    {
        return $this->converted ? $this->converted : $this->rates;
    }

    /**
     * Set all requested currency conversions.
     *
     * @param array
     * @return self
     */
    public function setConverted($converted)
    {
        $this->converted = $converted;

        return $this;
    }

    /**
     * Magic getter function for getting property values.
     *
     * @param string $name Property name
     * @return mixed
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
