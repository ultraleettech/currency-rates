<?php

namespace Ultraleet\CurrencyRates;

use Illuminate\Http\Request;
use Ultraleet\CurrencyRates\Contracts\Provider as ProviderContract;

abstract class AbstractProvider implements ProviderContract
{
    protected $config = [];

    /**
     * Set the configuration array for this provider.
     *
     * @param array
     *
     * @return \Ultraleet\CurrencyRates\AbstractProvider
     */
    public function configure($config)
    {
        $this->config = $config;

        return $this;
    }
}
