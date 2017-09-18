# Changelog

## Unreleased
### Added
- Fluent interface for API drivers.

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
