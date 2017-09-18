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

In the initial version, the CurrencyRates API exposed two methods for each service driver. One was used for querying the latest exchange rates, and the other for retrieving historical data. Those methods still work, and examples can be found below. However, in version 1.1 we provided a fluent interface for interacting with the API, which the documentation now emphasizes.

Note, that the following code snippets assume that you have your service object instantiated somehow (*e.g.* via dependency injection or by fetching from a service container) and stored in a variable called `$currencyRates`. If you are using Laravel, you can skip all that, and simply replace `$currencyRates->...` with `CurrencyRates::...` to use the facade instead. 

### Configuration

Some drivers require configuration to connect to the API, such as an app ID or API key. To provide one, you can simply chain in a `configure()` call right after instantiating the driver:

```php
$config = config('services.foo'); // Laravel example for fetching the config array
$result = $currencyRates->driver('foo')->configure($config)->...
```

Note, that you will only need to provide configuration to a driver once per request cycle. Subsequent API calls will remember the config.

### Latest/Current Rates

To get the latest rates for the default base currency (EUR), from the [fixer.io](http://fixer.io) API, all you need to do is this:

```php
$result = $currencyRates->driver('fixer')->get();

// this also works:
$result = $currencyRates->driver('fixer')->latest();
```

To get the rates for a different base currency:

```php
$result = $currencyRates->driver('fixer')->base('USD')->get();

// this also works:
$result = $currencyRates->driver('fixer')->latest('USD');
```

These calls return the rates for all currencies provided by the service driver. You can optionally specify the target currencies:

```php
$result = $currencyRates->driver('fixer')->target(['USD', 'GBP'])->get();
$result = $currencyRates->driver('fixer')->base('USD')->target(['EUR', 'GBP'])->get();

// you can provide a single target currency as a string
$result = $currencyRates->driver('fixer')->target('USD')->get();

// this also works:
$result = $currencyRates->driver('fixer')->latest('USD', ['EUR', 'GBP']);
```

### Historical Rates

For historical rates, you need to specify the date via the `date()` method. This method supports date strings as well as DateTime objects:

```php
$result = $currencyRates->driver('fixer')->date('2001-01-03')->get();
$result = $currencyRates->driver('fixer')->date('2001-01-03')->base('USD')->get();
$result = $currencyRates->driver('fixer')->date('2001-01-03')->base('USD')->target('EUR')->get();

// Alternatively, you can use the following. Note, that the date provided
// needs to be a DateTime object in this formulation:
$result = $currencyRates->driver('fixer')->historical(new \DateTime('2001-01-03'));
$result = $currencyRates->driver('fixer')->historical(new \DateTime('2001-01-03'), 'USD');
$result = $currencyRates->driver('fixer')->historical(new \DateTime('2001-01-03'), 'USD', ['EUR']);
```

### Fluent Interface Notes

The `base`, `target`, and `date` methods set the values of the parameters used in the API query performed by the `get` method. This means, that any previous values that have not been explicitly reset will be reused when making subsequent calls to the API.

You can simply set those parameters when you need to query different base/target currencies, or different dates. However, what if you want to query latest rates after making a historical query? Simple - you can just call `date()` without arguments:

```php
$historical = $currencyRates->driver('fixer')->date('2001-01-03')->get();
$latest = $currencyRates->driver('fixer')->date()->get();
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

Creating your own driver is easy. To get started, copy the `src/Providers/FixerProvider.php` file to your Laravel project, and name it by the service you want to support. Let's call it `FooProvider` and save it as `app/Currency/FooProvider.php`.

Now, edit the contents of the file to rename the class and provide your implementation. You will notice that the only methods implemented there are `latest` and `historical` - you should never need to override any of the fluent interface methods, since those are simply proxies for the lower level `latest`/`historical` calls issued by the `get` method in `AbstractProvider`.

If the API you are connecting to requires any configuration (such as an app ID or API key), you can access the data passed via the `AbstractProvider::configure()` method stored in `$this->config`.

### Laravel

Finally, you will need to register your new driver. To do so, either create a new Laravel service provider, or use your application service provider in `app/Providers/AppServiceProvider.php`. Add the following to the boot() method:

```php
use Ultraleet\CurrencyRates\CurrencyRatesManager;
use GuzzleHttp\Client as GuzzleClient;
use App\Currency\FooProvider;

public function boot(CurrencyRatesManager $manager)
{
    $manager->extend('foo', function ($app) {
        return new FooProvider(new GuzzleClient);
    });
}
```

That's it! You can now construct your custom driver with `\CurrencyRates::driver('foo')`.

### Non-Laravel Projects

While you could use the `CurrencyRates::extend()` method to register your custom driver, which works the same as the `CurrencyRatesManager::extend()` for Laravel applications above (except for one difference - the closure you provide does not take the `$app` argument), it might be easier to simply extend the base class itself, and implement a `create[Name]Driver()` method for constructing the provider instance:

```php
use Ultraleet\CurrencyRates\CurrencyRates;
use GuzzleHttp\Client as GuzzleClient;
use Namespace\Path\To\FooProvider;      // replace with your actual class path

class ExtendedCurrencyRates extends CurrencyRates
{
    protected function createFooDriver()
    {
        return new FooProvider(new GuzzleClient);
    }

} 
```

(Note: driver name strings (when calling the `driver('name')` method) are in *snake_case* while they are expected to be in *StudlyCase* in the above `create[Name]Driver` method name. A driver called *'my_provider'* is hence constructed in a method called *createMyProvider()*.)

Then, all you need to do is register or instantiate the extended service instead of the original one.
