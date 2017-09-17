# CurrencyRates

[![PHP version](https://badge.fury.io/ph/ultraleet%2Fcurrency-rates.svg)](https://badge.fury.io/ph/ultraleet%2Fcurrency-rates)
[![Build Status](https://travis-ci.org/ultraleettech/currency-rates.svg)](https://travis-ci.org/ultraleettech/currency-rates)
[![Latest Stable Version](https://poser.pugx.org/ultraleet/currency-rates/version)](https://packagist.org/packages/ultraleet/currency-rates)
[![Total Downloads](https://poser.pugx.org/ultraleet/currency-rates/downloads)](https://packagist.org/packages/ultraleet/currency-rates)
[![License](https://poser.pugx.org/ultraleet/currency-rates/license)](https://packagist.org/packages/ultraleet/currency-rates)

A PHP library for interacting with various currency exchange rates APIs. It provides a simple factory interface for constructing a wrapper for a chosen service which exposes a simple unified API for querying currency exchange rates.

## Services
Currently available:
- [Fixer.io](http://fixer.io)

We are working on adding drivers for other services. Our API is easily extendable so you can add your own drivers (see below for instructions). When you do, feel free to contact us and send your implementation so we can integrate it into the official package.

## Installation

To get started, add the package to your project by issuing the following command:

    composer require ultraleet/currency-rates

### Laravel <5.5

Laravel 5.5 introduced package discovery, which CurrencyRates fully utilizes. However, if you are using an earlier version of Laravel, you will need to register the service provider in your `config/app.php` file:

```php
'providers' => [
    // Other service providers...

    Ultraleet\CurrencyRates\CurrencyRatesServiceProvider::class,
],
```

Also, in the same file, add the `CurrencyRates` facade into the `aliases` array:

```php
'CurrencyRates' => Ultraleet\CurrencyRates\Facades\CurrencyRates::class,
```

## Getting Started

### Laravel

CurrencyRates comes shipped with a service provider that conveniently registers the component with Laravel's service container, as well as a facade for global access. Hence, it requires no further setup.

### Symfony

You will need to configure your service container to load CurrencyRates when needed. To do that, simply append this to your `services.yml` config file:

```yaml
    currency_rates:
        class: Ultraleet\CurrencyRates\CurrencyRates
        public: true    # Symfony 3.3+
```

The last line is optional and only required if you wish to fetch the service directly from the service container (*e.g.* via `$this->get('currency_rates')` in your controllers). However, it is recommended to use service injection instead.

You can now inject it into your service contructors or controller actions:

```php
use Ultraleet\CurrencyRates\CurrencyRates;

class YourService
{
    private $currencyRates;

    public function __construct(CurrencyRates $currencyRates)
    {
        $this->currencyRates = $currencyRates;
    }
}
```

### Other

Most modern frameworks are designed using the [Inversion of Control](https://en.wikipedia.org/wiki/Inversion_of_control) principle and implement some sort of a service container that can be used to locate services and/or inject dependencies. You should therefore register CurrencyRates with that service container. The details vary depending on the framework; you should look it up in the documentation if you don't know how to do that. You can refer to the instructions for Symfony above for general guidelines.

If you are not using a framework, it is still recommended to implement those principles into your projects. However, if that would be overkill for your simple project, or you simply don't want to / can't do that for some reason, you can simply construct CurrencyRates service directly, whenever needed:

```php
use Ultraleet\CurrencyRates\CurrencyRates;

$currencyRates = new CurrencyRates;
```

## Usage

The following code snippets assume that you have your service object instantiated somehow (*e.g.* via dependency injection or by fetching from a service container) and stored in a variable called `$currencyRates`. If you are using Laravel, you can skip all that, and simply replace `$currencyRates->...` with `CurrencyRates::...` to use the facade instead. 

The CurrencyRates API exposes two methods for each service driver. One is used for querying the latest exchange rates, and the other is for retrieving historical data.

### Latest/Current Rates

To get the latest rates for the default base currency (EUR), from the [fixer.io](http://fixer.io) API, all you need to do is this:

```php
$result = $currencyRates->driver('fixer')->latest();
```

To get the rates for a different base currency, you will need to provide its code as the first argument:

```php
$result = $currencyRates->driver('fixer')->latest('USD');
```

These calls return the rates for all currencies provided by the service driver. You can optionally specify the target currencies in an array as the second argument:

```php
$result = $currencyRates->driver('fixer')->latest('USD', ['EUR', 'GBP']);
```

### Historical Rates

Historical rates are provided via the `historical()` driver method. This method takes date (as a DateTime object) as its first argument, and optional base and target currencies as its second and third arguments:

```php
$result = $currencyRates->driver('fixer')->historical(new \DateTime('2001-01-03'));
$result = $currencyRates->driver('fixer')->historical(new \DateTime('2001-01-03'), 'USD');
$result = $currencyRates->driver('fixer')->historical(new \DateTime('2001-01-03'), 'USD', ['EUR', 'GBP']);
```

## Response

Rates are returned as an object of class `Response`. It provides 3 methods to get the data provided by the API call:

```php
$result = $currencyRates->driver('fixer')->latest('USD', ['EUR', 'GBP']);

$date = $result->getDate();     // Contains the date as a DateTime object
$rates = $result->getRates();   // Array of exchange rates
$gbp = $result->getRate('GBP'); // Rate for the specific currency, or null if none was provided/requested
```

It also implements a magic getter for conveniently retrieving the results as properties. You can thus simply write the following:

```php
$date = $result->date;          // Contains the date as a DateTime object
$rates = $result->rates;        // Array of exchange rates
$gbp = $result->rates['GBP'];   // Rate for the specific currency
```

## Exceptions

CurrencyRate provides 2 exceptions it can throw when encountering errors. `ConnectionException` is thrown when there is a problem connecting to the API end point. For invalid requests and unexpected responses, it throws a `ResponseException`.

## Custom Providers

### Laravel

Creating your own driver is easy. To get started, copy the `src/Providers/FixerProvider.php` file to your Laravel project, and name it by the service you want to support. Let's call it `FooProvider` and save it as `app/Currency/FooProvider.php`.

Now, edit the contents of the file to rename the class and provide your implementation. If the API you are connecting to requires no configuration (such as an App ID or API key), it should be straight forward enough. Otherwise, you can add your configuration as an argument to your constructor:

```php
protected $config;

public function __construct(GuzzleClient $guzzle, $config)
{
    $this->guzzle = $guzzle;
    $this->config = $config;
}
```

Then, add the configuration you need into `config/services.php`:

```php
'foo' => [
    'api_id' => env('FOO_API_ID'),
    'api_key' => env('FOO_API_KEY'),
],
```

Finally, you will need to register your new provider. To do so, either create a new Laravel service provider, or use your application service provider in `app/Providers/AppServiceProvider.php`. Add the following to the boot() method:

```php
use Ultraleet\CurrencyRates\CurrencyRatesManager;
use GuzzleHttp\Client as GuzzleClient;
use App\Currency\FooProvider;

public function boot(CurrencyRatesManager $manager)
{
    $manager->extend('foo', function ($app) {
        return new FooProvider(new GuzzleClient, config('services.foo'));
    });
}
```

Note, that if your API doesn't require any configuration, simply omit the second argument. Basically, use whatever signature you set your provider's constructor up to require.

That's it! You can now construct your custom driver with `\CurrencyRates::driver('foo')`.

### Non-Laravel Projects

Creating the actual driver is pretty much identical to how you would approach this in a Laravel application, so refer to the above instructions for that. However, instead of registering the driver via the extend method (which is also provided by the `CurrencyRates` class with an identical implementation except for one difference - the closure you provide does not take the `$app` argument), it might be easier to simply extend the base class itself, and implement a `create[Name]Driver()` method for constructing the provider instance:

```php
use Ultraleet\CurrencyRates\CurrencyRates;
use GuzzleHttp\Client as GuzzleClient;
use Namespace\Path\To\FooProvider;      // replace with your actual class path

class ExtendedCurrencyRates extends CurrencyRates
{
    protected function createFooDriver()
    {
        // fetch (e.g. from environment or application config), if needed:
        $config = ...

        return new FooProvider(new GuzzleClient, $config);
    }

} 
```

(Note: driver name strings (when calling the `driver('name')` method) are in *snake_case* while they are expected to be in *StudlyCase* in the above `create[Name]Driver()` method. *'my_provider'* hence becomes *createMyProvider()*, and vice-versa.)

Then, all you need to do is register or instantiate the extended service instead of the original one.
