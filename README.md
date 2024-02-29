# A small Laravel wrapper for the Payconiq API.

A very small package providing some functions to access the Payconiq API. Inspired by https://github.com/optiosteam/payconiq-client-php

## Installation

You can install the package via composer:

```bash
composer require anykrowd/laravel-payconiq-api
```

Publish the config file of this package:

```bash
php artisan vendor:publish --provider="Anykrowd\PayconiqApi\PayconiqApiServiceProvider"
```