<?php

namespace Ultraleet\CurrencyRates\Providers;

use Ultraleet\CurrencyRates\AbstractProvider;
use Ultraleet\CurrencyRates\Result;
use Ultraleet\CurrencyRates\Exceptions\ConnectionException;
use Ultraleet\CurrencyRates\Exceptions\ResponseException;
use GuzzleHttp\Client as GuzzleClient;
use DateTime;
use InvalidArgumentException;
use GuzzleHttp\Exception\TransferException;

class YahooProvider extends AbstractProvider
{
    protected $guzzle;
    protected $url = 'http://query.yahooapis.com/v1/public/yql';
    protected $urlParams = [
        'env' => 'store://datatables.org/alltableswithkeys',
        'format' => 'json',
    ];

    /**
     * @var array List of supported currencies.
     */
    protected $currencies = [
        'KRW', 'XAG', 'VND', 'BOB', 'MOP', 'BDT', 'MDL', 'VEF', 'GEL', 'ISK',
        'BYR', 'THB', 'MXV', 'TND', 'JMD', 'DKK', 'SRD', 'BWP', 'NOK', 'MUR',
        'AZN', 'INR', 'MGA', 'CAD', 'XAF', 'LBP', 'XDR', 'IDR', 'IEP', 'AUD',
        'MMK', 'LYD', 'ZAR', 'IQD', 'XPF', 'TJS', 'CUP', 'UGX', 'NGN', 'PGK',
        'TOP', 'TMT', 'KES', 'CRC', 'MZN', 'BYN', 'SYP', 'ANG', 'ZMW', 'BRL',
        'BSD', 'NIO', 'GNF', 'BMD', 'SLL', 'MKD', 'BIF', 'LAK', 'BHD', 'SHP',
        'BGN', 'SGD', 'CNY', 'EUR', 'TTD', 'SCR', 'BBD', 'SBD', 'MAD', 'GTQ',
        'MWK', 'PKR', 'ITL', 'PEN', 'AED', 'LVL', 'XPD', 'UAH', 'FRF', 'LRD',
        'LSL', 'SEK', 'RON', 'XOF', 'COP', 'CDF', 'USD', 'TZS', 'GHS', 'NPR',
        'ZWL', 'SOS', 'DZD', 'FKP', 'LKR', 'JPY', 'CHF', 'KYD', 'CLP', 'IRR',
        'AFN', 'DJF', 'SVC', 'PLN', 'PYG', 'ERN', 'ETB', 'ILS', 'TWD', 'KPW',
        'SIT', 'GIP', 'BND', 'HNL', 'CZK', 'HUF', 'BZD', 'DEM', 'JOD', 'RWF',
        'LTL', 'RUB', 'RSD', 'WST', 'XPT', 'NAD', 'PAB', 'DOP', 'ALL', 'HTG',
        'AMD', 'KMF', 'MRO', 'HRK', 'HTG', 'KHR', 'PHP', 'CYP', 'KWD', 'XCD',
        'XCP', 'CNH', 'SDG', 'CLF', 'KZT', 'TRY', 'FJD', 'NZD', 'BAM', 'BTN',
        'STD', 'VUV', 'MVR', 'AOA', 'EGP', 'QAR', 'OMR', 'CVE', 'KGS', 'MXN',
        'MYR', 'GYD', 'SZL', 'YER', 'SAR', 'UYU', 'GBP', 'UZS', 'GMD', 'AWG',
        'MNT', 'XAU', 'HKD', 'ARS', 'HUX', 'FKP', 'CHF', 'GEL', 'MOP', 'SIT',
        'KMF', 'ZWL', 'BAM', 'AOA', 'CNY', 'TTD', 'BRX', 'ECS'
    ];


    /**
     * Class constructor.
     *
     * @param \GuzzleHttp\Client $guzzle
     * @return void
     */
    public function __construct(GuzzleClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Get latest currency exchange rates.
     *
     * @param  string  $base
     * @param  array   $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function latest($base = 'EUR', $targets = [])
    {
        // Query all supported targets in case none were specified
        if (empty($targets)) {
            $targets = $this->currencies;

            // unset base currency
            if ($key = array_search($base, $targets)) {
                unset($targets[$key]);
            }
        }

        // Build query
        $targets = $this->targetsToString($targets, $base);
        $yql = sprintf('select * from yahoo.finance.xchange where pair in (%s)', $targets);

        // Query API
        $response = $this->query($yql);

        // Parse response
        if (isset($response['rate']) && is_array($response['rate'])) {
            $rates = [];
            $date = 0;
            if (isset($response['rate']['Rate'])) {
                // Wrap single result in an array
                $response['rate'] = [$response['rate']];
            }
            foreach ($response['rate'] as $result) {
                // No result, skip
                if ('N/A' == $result['Rate']) {
                    continue;
                }

                // Save the latest timestamp
                if ($date < $ts = strtotime($result['Date'].' '.$result['Time'])) {
                    $date = $ts;
                }

                // Save rate
                $currency = substr($result['Name'], strpos($result['Name'], '/') + 1);
                $rates[$currency] = $result['Rate'];
            }

            return new Result(
                $base,
                new DateTime('@'.$date),
                $rates
            );
        } else {
            throw new ResponseException('Invalid results.');
        }
    }

    /**
     * Overriden method - historical rates are unsupported by Yahoo Finance API
     *
     * @param mixed $date Can be a string, a DateTime object, or null to unset.
     * @return self
     */
    public function date($date = null)
    {
        trigger_error('Yahoo Finance API does not provide historical rates', E_USER_WARNING);

        return $this;
    }

    /**
     * Unsupported by Yahoo Finance API.
     *
     * @param  \DateTime $date
     * @param  string    $base
     * @param  array     $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    public function historical($date, $base = 'EUR', $targets = [])
    {
        // Yahoo Finance API no longer provides historical rates...
        // Let's just trigger a warning and return latest rates instead.
        $this->date($date);

        return $this->latest($base, $targets);
    }

    /**
     * Perform the API query and return results as an array.
     *
     * @param  string $yql YQL query to send.
     * @return array
     */
    protected function query($yql)
    {
        $params = $this->urlParams;
        $params['q'] = $yql;
        $url = $this->url . '?' . http_build_query($params);

        // query the API
        try {
            $response = $this->guzzle->request('GET', $url);
        } catch (TransferException $e) {
            throw new ConnectionException($e->getMessage());
        }

        // process response
        $response = json_decode($response->getBody(), true);

        if (isset($response['query']) && isset($response['query']['results'])) {
            return $response['query']['results'];
        } else {
            throw new ResponseException('Response body is malformed.');
        }
    }

    /**
     * Convert targets array to string for using in the YQL query.
     *
     * @param  array  $targets
     * @param  string $prefix  Prefix to add to each currency (e.g. base currency)
     * @param  string $suffix  Suffix to add to each currency
     * @return string
     */
    protected static function targetsToString($targets, $prefix = '', $suffix = '')
    {
        $array = [];
        foreach ($targets as $currency) {
            $array[] = '"' . $prefix . $currency . $suffix . '"';
        }

        return implode(', ', $array);
    }
}
