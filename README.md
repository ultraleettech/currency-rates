# CurrencyRates
A PHP library for interacting with various currency exchange rates APIs. It provides a simple factory interface for constructing a wrapper for a chosen service which exposes a simple unified API for querying currency exchange rates.

Note: this package is currently only available for Laravel applications. Technically, it is possible to use it in other types of projects, but doing so is currently not supported, mainly for extensibility reasons. Non-Laravel support is currently under development and should be available shortly.

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

## Usage

The CurrencyRates API exposes two methods for each service driver. One is used for querying the latest exchange rates, and the other is for retrieving historical data.

### Latest/Current Rates

To get the latest rates for the default base currency (EUR), from the [fixer.io](http://fixer.io) API, all you need to do is this:

```php
use CurrencyRates;

$result = CurrencyRates::driver('fixer')->latest();
```

To get the rates for a different base currency, you will need to provide its code as the first argument:

```php
use CurrencyRates;

$result = CurrencyRates::driver('fixer')->latest('USD');
```

These calls return the rates for all currencies provided by the service driver. You can optionally specify the target currencies in an array as the second argument:

```php
use CurrencyRates;

$result = CurrencyRates::driver('fixer')->latest('USD', ['EUR', 'GBP']);
```

### Historical Rates

Historical rates are provided via the `historical()` driver method. This method takes date (as a DateTime object) as its first argument, and optional base and target currencies as its second and third arguments:

```php
use CurrencyRates;
use DateTime;

$result = CurrencyRates::driver('fixer')->historical(new DateTime('2001-01-03'));
$result = CurrencyRates::driver('fixer')->historical(new DateTime('2001-01-03'), 'USD');
$result = CurrencyRates::driver('fixer')->historical(new DateTime('2001-01-03'), 'USD', ['EUR', 'GBP']);
```

## Response

Rates are returned as an object of class `Response`. It provides 3 methods to get the data provided by the API call:

```php
use CurrencyRates;

$result = CurrencyRates::driver('fixer')->latest('USD', ['EUR', 'GBP']);

$date = $result->getDate();     // Contains the date as a DateTime object
$rates = $result->getRates();   // Array of exchange rates
$gbp = $result->getRate('GBP'); // Rate for the specific currency, or null if none was provided/asked for
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

Creating your own driver is easy. To get started, copy the `srt/Providers/FixerProvider.php` to your Laravel project, and name it by the service you want to support. Let's call it `FooProvider` and save it as `app/Currency/FooProvider.php`.

Now, edit the contents of the file to rename your class and provide your implementation. If the API you are connecting to requires no configuration (such as App ID or API key), it should be straight forward enough. Otherwise, you can add your configuration as an argument to your constructor:

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
