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

class FixerProvider extends AbstractProvider
{
    protected $guzzle;
    protected $url = "http://api.fixer.io";

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
        return $this->query('latest', $base, $targets);
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
        return $this->query($date->format('Y-m-d'), $base, $targets);
    }

    /**
     * Get historical currency exchange rates.
     *
     * @param  string $date 'latest' for latest, 'Y-m-d' date for historical.
     * @param  string $base
     * @param  array  $targets
     * @return \Ultraleet\CurrencyRates\Contracts\Result
     */
    protected function query($date, $base, $targets)
    {
        $url = $this->url . '/' . $date;
        $query = [];

        // add base to query string
        if ($base !== 'EUR') {
            $query[] = 'base=' . $base;
        }

        // add symbols to query string
        if (!empty($targets)) {
            $query[] = 'symbols=' . implode(',', $targets);
        }

        // append query string to url
        if (!empty($query)) {
            $url .= '?' . implode('&', $query);
        }

        // query the API
        try {
            $response = $this->guzzle->request('GET', $url);
        } catch (TransferException $e) {
            throw new ConnectionException($e->getMessage());
        }

        // process response
        $response = json_decode($response->getBody(), true);
        if (isset($response['rates']) && is_array($response['rates']) &&
            isset($response['base']) && isset($response['date'])) {
            return new Result(
                $response['base'],
                new DateTime($response['date']),
                $response['rates']
            );
        } elseif (isset($response['error'])) {
            throw new ResponseException($response['error']);
        } else {
            throw new ResponseException('Response body is malformed.');
        }
    }
}