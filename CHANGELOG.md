# Changelog

## Unreleased
### Added
- Currency conversion. (#1)

### Deprecated
- `latest` and `historical` API methods. Use fluent interface instead! Will be removed in v2.0.

## [1.1.0] - 2017-09-19
### Added
- Fluent interface for API drivers.
- Driver for Yahoo Finance latest exchange rates.

### Changed
- Improve usage instructions.
- Improve the way configuration is passed to custom providers.

## [1.0.0] - 2017-09-13
### Added
- Laravel manager class for instantiating API drivers.
- Laravel service provider and facade.
- Provider interface with 2 methods (`latest` and `historical`).
- Driver for [fixer.io](http://fixer.io).
- Result interface for providing currency rate data returned by an API as an object.
- Laravel 5.5+ package auto-discovery.
- Generic service class for using the package in non-Laravel contexts.
- Support for implementing custom API drivers.
