<?php

namespace Ultraleet\CurrencyRates\Providers;

use Ultraleet\CurrencyRates\AbstractProvider;
use Ultraleet\CurrencyRates\Result;
use DateTime;
use InvalidArgumentException;

class DummyProvider extends AbstractProvider
{

    /**
     * @var List of currencies to use. Mimics the currencies provided by fixer.io (ECB).
     */
    private $currencies = [
        'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'EUR', 'GBP',
        'HKD', 'HRK', 'HUF', 'IDR', 'ILS', 'INR', 'JPY', 'KRW', 'MXN', 'MYR',
        'NOK', 'NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TRY',
        'USD', 'ZAR'
    ];

    /**
     * Get latest currency exchange rates.
     *
     * @param  string  $base
     * @param  array   $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function latest($base = 'EUR', $targets = [])
    {
        $date = date('Y-m-d');
        $day = date('w');

        if (!$day || $day == 6) { // saturday or sunday
            $date = date('Y-m-d', strtotime('last Friday'));
        }

        return $this->historical(new DateTime($date), $base, $targets);
    }

    /**
     * Get historical currency exchange rates.
     *
     * @param  \DateTime $date
     * @param  string    $base
     * @param  array     $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function historical($date, $base = 'EUR', $targets = [])
    {
        $key = array_search($base, $this->currencies);
        if ($key === false) {
            throw new InvalidArgumentException("Invalid base currency specified: $base");
        }

        if (empty($targets)) {
            $currencies = $this->currencies;
            unset($currencies[$key]);
        } else {
            $currencies = $targets;
        }

        // generate dummy results
        $res = [];
        foreach ($currencies as $currency) {
            if (!in_array($currency, $this->currencies)) {
                throw new InvalidArgumentException("Invalid target currency specified: $currency");
            }

            $res[$currency] = mt_rand(10000, 150000) / 100000;
        }

        // return the results
        return new Result($base, $date, $res);
    }
}