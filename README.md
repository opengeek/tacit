# Tacit

[![Build Status](https://travis-ci.org/opengeek/tacit.svg?branch=master)](https://travis-ci.org/opengeek/tacit)

Tacit is a high-performance REST server library for PHP 5.4+ built on the [Slim micro framework](http://www.slimframework.com/).


## Features

* A set of base RESTful controller classes supporting the HAL JSON format
    * RESTful item, collection, and error formats
    * Provides a set of RESTful exceptions for all common HTTP responses
    * Provides default RESTful behavior for persistent items/collections
    * Unit testable via Slim
* Allows custom authorization implementations with flexible integration points
* Extensible output transformers via [Fractal](https://github.com/thephpleague/fractal)
* A flexible persistence layer
    * Support for MongoDB provided via [Monga](https://github.com/thephpleague/monga)
    * Support for RethinkDB provided via [PHP-RQL](http://danielmewes.github.io/php-rql/)
    * Simple and extensible validation library tightly integrated


## Install

Via Composer

```bash
$ composer require tacit/tacit
```

## System Requirements

The following versions of PHP are supported by this version:

* PHP 5.4
* PHP 5.5
* PHP 5.6


## Documentation

*TODO*


## Testing

```bash
$ phpunit
```


## Contributing

Please see [CONTRIBUTING](./CONTRIBUTING.md) for details.


## Credits

* [Jason Coward](https://github.com/opengeek/)
* [John Peca](https://github.com/TheBoxer/)
* [All Contributors](https://github.com/opengeek/tacit/graphs/contributors)


## License

The MIT License (MIT). Please see the [License file](./LICENSE) for more information.
