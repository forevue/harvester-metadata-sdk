# Harvester Metadata SDK

[![Tests](https://github.com/forevue/harvester-metadata-sdk/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/forevue/harvester-metadata-sdk/actions/workflows/tests.yml)
[![Formats](https://github.com/forevue/harvester-metadata-sdk/actions/workflows/formats.yml/badge.svg?branch=main)](https://github.com/forevue/harvester-metadata-sdk/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/forevue/harvester-metadata-sdk/version)](//packagist.org/packages/forevue/harvester-metadata-sdk)
[![Total Downloads](https://poser.pugx.org/forevue/harvester-metadata-sdk/downloads)](//packagist.org/packages/forevue/harvester-metadata-sdk)
[![License](https://poser.pugx.org/forevue/harvester-metadata-sdk/license)](//packagist.org/packages/forevue/harvester-metadata-sdk)

## Installation

> Requires [PHP 8.2+](https://php.net/releases)

You can install the package via composer:

```bash
composer require forevue/harvester-metadata-sdk
```

## Usage

> This is a cowboy-style SDK, you connect to the database directly.

### Getting started

```php
use Forevue\HarvesterMetadataSdk\Client;

// This creates a PDO instance behind the scenes
$client = new Client(
    host: 'localhost',
    password: 'root',
    port: 5432,
    user: 'postgres',
    database: 'postgres',
    driver: 'pgsql',
)

// Or,
$client = new Client(dsn: 'pgsql:...')

// Or,
$client = new Client(pdo: new PDO(...))
````

### Querying

```php

/** @var \Forevue\HarvesterMetadataSdk\Client $client */
$client->sources()->all();
$client->sources()->find(1234);
$client->sources()->findByUrn('urn:forevue:source:code-hash:static-hash');

$client->providers()->all();
$client->providers()->find(1234);
$client->providers()->findByUrn('urn:forevue:provider:code-hash:static-hash');

/** @var \Forevue\HarvesterMetadataSdk\DataObjects\Source $source */
$source->id();
$source->name(); // etc
$source->isDirty() // this is the only computed property
$source->subSources(); // returns the child sources
$source->provider(); // returns the parent provider object

// $source->crawlInterval(); and $source->recrawlInterval() returns a array{int, int} not an object

/** @var \Forevue\HarvesterMetadataSdk\DataObjects\Provider $provider */
$provider->id();
$provider->name(); // etc
$provider->sources(); // returns the provider's sources
```

## Testing

```bash
composer test
```

**harvester-metadata-sdk** was created by **[Félix Dorn](https://felixdorn.fr)** under the *
*[MIT license](https://opensource.org/licenses/MIT)**.
