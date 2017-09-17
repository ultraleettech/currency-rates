<?php

namespace Ultraleet\CurrencyRates;

use Ultraleet\CurrencyRates\Contracts\Factory;
use Ultraleet\CurrencyRates\Providers\DummyProvider;
use Ultraleet\CurrencyRates\Providers\FixerProvider;
use Illuminate\Support\Manager;
use GuzzleHttp\Client as GuzzleClient;
use Closure;

class CurrencyRatesManager extends Manager implements Factory
{
    /**
     * The CurrencyRates factory instance.
     *
     * @var \Ultraleet\CurrencyRates\CurrencyRates
     */
    protected $factory;

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application   $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->factory = new CurrencyRates;
    }

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        return $this->factory->driver($driver);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->factory->getDefaultDriver();
    }

    /**
     * Set the default driver name.
     *
     * @param string
     */
    public function setDefaultDriver($name)
    {
        $this->factory->setDefaultDriver($name);

        return $this;
    }

    /**
     * Get all of the created drivers.
     *
     * @return array
     */
    public function getDrivers()
    {
        return $this->factory->getDrivers();
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
        $this->factory->extend($driver, $callback);

        return $this;
    }
}
