<?php

namespace Tests\Providers;

use Ultraleet\CurrencyRates\Providers\FixerProvider;
use DateTime;
use Mockery as m;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class FixerProviderTest extends PHPUnit_Framework_TestCase
{
    protected $responses = [
        'success' => [
            'base' => 'EUR',
            'date' => '2017-09-13',
            'rates' => [
                    'GBP' => 0.90243,
                    'USD' => 1.1979,
            ],
        ],
        'error' => [
            'error' => 'Not found',
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
        $driver = new FixerProvider($this->mock($this->responses['success']));
        $result = $driver->latest('USD', ['EUR']); // just setting args to mock coverage here

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
        $this->assertEquals('0.90243', $result->getRate('GBP'));
    }

    public function testHistoricalReturnsValidResult()
    {
        $driver = new FixerProvider($this->mock($this->responses['success']));
        $result = $driver->historical(new DateTime($this->responses['success']['date']));

        $this->assertInstanceOf('Ultraleet\CurrencyRates\Result', $result);
        $this->assertEquals('0.90243', $result->getRate('GBP'));
    }

    /**
     * @expectedException Ultraleet\CurrencyRates\Exceptions\ResponseException
     * @expectedExceptionMessage Not found
     */
    public function testQueryThrowsExceptionWhenAPIRequestReturnsError()
    {
        $driver = new FixerProvider($this->mock($this->responses['error']));
        $result = $driver->latest();
    }

    /**
     * @expectedException Ultraleet\CurrencyRates\Exceptions\ResponseException
     * @expectedExceptionMessage Response body is malformed
     */
    public function testQueryThrowsExceptionWhenAPIRequestReturnsInvalidResponse()
    {
        $driver = new FixerProvider($this->mock($this->responses['invalid']));
        $result = $driver->latest();
    }

    /**
     * @expectedException Ultraleet\CurrencyRates\Exceptions\ConnectionException
     */
    public function testQueryThrowsConnectionExceptionWhenClientThrowsTransferException()
    {
        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andThrow('GuzzleHttp\Exception\TransferException');

        $driver = new FixerProvider($client);
        $result = $driver->latest();
    }
}
