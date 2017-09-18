<?php

namespace Ultraleet\CurrencyRates;

use Ultraleet\CurrencyRates\Contracts\Factory;
use Ultraleet\CurrencyRates\Providers\DummyProvider;
use Ultraleet\CurrencyRates\Providers\FixerProvider;
use Ultraleet\CurrencyRates\Providers\YahooProvider;
use GuzzleHttp\Client as GuzzleClient;
use Closure;
use InvalidArgumentException;

class CurrencyRates implements Factory
{
    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created drivers.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * The name of the default driver.
     *
     * @var string
     */
    protected $defaultDriver = 'fixer';

    /**
     * Create an instance of the specified driver.
     *
     * @return \Ultraleet\CurrencyRates\AbstractProvider
     */
    protected function createFixerDriver()
    {
        return new FixerProvider(new GuzzleClient());
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Ultraleet\CurrencyRates\AbstractProvider
     */
    protected function createYahooDriver()
    {
        return new YahooProvider(new GuzzleClient());
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Ultraleet\CurrencyRates\AbstractProvider
     */
    protected function createDummyDriver()
    {
        return new DummyProvider();
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultDriver;
    }

    /**
     * Set the default driver name.
     *
     * @param string
     */
    public function setDefaultDriver($name)
    {
        $this->defaultDriver = $name;

        return $this;
    }

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        // First, we will check if a custom creator has been created for the
        // specified driver. If not, we will create a native driver instead.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } else {
            $method = 'create' . static::studlyCase($driver) . 'Driver';

            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }
        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string  $driver
     * @return mixed
     */
    protected function callCustomCreator($driver)
    {
        return $this->customCreators[$driver]();
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get all of the created drivers.
     *
     * @return array
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    public static function studlyCase($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }
}
