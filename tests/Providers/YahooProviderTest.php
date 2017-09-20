<?php

namespace Tests\Providers;

use Ultraleet\CurrencyRates\Providers\YahooProvider;
use DateTime;
use Mockery as m;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class YahooProviderTest extends PHPUnit_Framework_TestCase
{
    protected $responses = [
        'success' => [
            'query' => [
                'count' => 1,
                'created' => '2017-09-18T13:47:13Z',
                'lang' => 'en-US',
                'results' => [
                    'rate' => [
                        [
                            'id' => 'EURGBP',
                            'Name' => 'EUR/GBP',
                            'Rate' => '0.8811',
                            'Date' => '9/18/2017',
                            'Time' => '2:47pm',
                            'Ask' => '0.8811',
                            'Bid' => '0.8811',
                        ],[
                            'id' => 'EURXXX',
                            'Name' => 'N/A',
                            'Rate' => 'N/A',
                            'Date' => 'N/A',
                            'Time' => 'N/A',
                            'Ask' => 'N/A',
                            'Bid' => 'N/A',
                        ],
                    ],
                ],
            ],
        ],
        'norate' => [
            'query' => [
                'results' => [
                ],
            ],
        ],
        'invalid' => [
            'random' => 'result',
        ],
    ];

    protected function mock($responseBody)
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode($responseBody));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        return $client;
    }

    public function testLatestReturnsValidResult()
    {
        $driver = new YahooProvider($this->mock($this->responses['success']));
        $result = $driver->latest('EUR', ['GBP']);

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
        $this->assertEquals('0.8811', $result->getRate('GBP'));
    }

    public function testHistoricalTriggersWarning()
    {
        if (class_exists('PHPUnit_Framework_Error_Warning')) {
            $this->expectException('PHPUnit_Framework_Error_Warning');
        } else {
            $this->expectException('PHPUnit\Framework\Error\Warning');
        }

        $this->expectExceptionMessage('Yahoo Finance API does not provide historical rates');

        $driver = new YahooProvider($this->mock($this->responses['success']));
        $result = $driver->historical(new DateTime('2001-01-03'), 'EUR', ['GBP']);
    }

    public function testHistoricalReturnsLatestResults()
    {
        // suppress warning
        $errorReporting = ini_get('error_reporting');
        ini_set('error_reporting', 0);

        $driver = new YahooProvider($this->mock($this->responses['success']));
        $result = $driver->historical(new DateTime('2001-01-03'), 'EUR', ['GBP']);

        // should proxy latest()
        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
        $this->assertEquals('0.8811', $result->getRate('GBP'));

        // reset error reporting
        ini_set('error_reporting', $errorReporting);
    }

    /**
     * @expectedException Ultraleet\CurrencyRates\Exceptions\ResponseException
     * @expectedExceptionMessage Response body is malformed
     */
    public function testQueryThrowsExceptionWhenAPIRequestReturnsInvalidResponse()
    {
        $driver = new YahooProvider($this->mock($this->responses['invalid']));
        $result = $driver->latest();
    }

    /**
     * @expectedException Ultraleet\CurrencyRates\Exceptions\ResponseException
     * @expectedExceptionMessage Invalid results
     */
    public function testQueryThrowsExceptionOnInvalidResults()
    {
        $driver = new YahooProvider($this->mock($this->responses['norate']));
        $result = $driver->latest();
    }

    /**
     * @expectedException Ultraleet\CurrencyRates\Exceptions\ConnectionException
     */
    public function testQueryThrowsConnectionExceptionWhenClientThrowsTransferException()
    {
        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andThrow('GuzzleHttp\Exception\TransferException');

        $driver = new YahooProvider($client);
        $result = $driver->latest();
    }
}
