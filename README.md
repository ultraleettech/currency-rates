# CurrencyRates
A PHP library for interacting with various currency exchange rates APIs. It provides a simple factory interface for constructing a wrapper for a chosen service which exposes a simple unified API for querying currency exchange rates.

## Services
Currently available:
- [Fixer.io](http://www.fixer.io)

We are working on adding drivers for other services. Our API is easily extendable so you can add your own drivers (see below for instructions). When you do, feel free to contact us and send your implementation so we can integrate it into the official package.

## Installation

To get started, add the package to your project by issuing the following command:

    composer require ultraleet/currency-rates

### Laravel <5.5

Laravel 5.5 introduced package discovery, which CurrencyRates fully utilizes. However, if you are using an earlier version of Laravel, you need to register the service provider in your `config/app.php` file:

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

### Latest/current Rates

To get the latest rates for the default base currency (EUR), from the <fixer.io> API, all you need to do is this:

```php
use CurrencyRates;

$rates = CurrencyRates::driver('fixer')->latest();
```

To get the rates for a different base currency, you will need to provide its code as the first argument:

```php
use CurrencyRates;

$rates = CurrencyRates::driver('fixer')->latest('USD');
```

These calls return the rates for all currencies provided by the service driver. You can optionally specify the target currencies in an array as the second argument:

```php
use CurrencyRates;

$rates = CurrencyRates::driver('fixer')->latest('USD', ['EUR', 'GBP']);
```

### Historical Rates

Historical rates are provided via the `historical()` driver method. This method takes date (as a DateTime object) as its first argument, and base and target currencies as its optional second and third arguments:

```php
use CurrencyRates;
use DateTime;

$rates = CurrencyRates::driver('fixer')->historical(new DateTime('2001-01-03'));
$rates = CurrencyRates::driver('fixer')->historical(new DateTime('2001-01-03'), 'USD');
$rates = CurrencyRates::driver('fixer')->historical(new DateTime('2001-01-03'), 'USD', ['EUR', 'GBP']);
```

## Response

Rates are returned as an object of class `Response`. It provides 3 methods to get the data provided by the API call:

```php
use CurrencyRates;

$rates = CurrencyRates::driver('fixer')->latest('USD', ['EUR', 'GBP']);

$date = $result->getDate();     // Contains the date as a DateTime object
$rates = $result->getRates();   // Array of exchange rates
$gbp = $result->getRate('GBP'); // Rate for the specific currency, or null if none was provided/asked for
```

## Exceptions

CurrencyRate provides 2 exceptions it can throw when encountering errors. `ConnectionException` is thrown when there is a problem connecting to the API end point. For invalid requests and unexpected responses, it throws a `ResponseException`.
