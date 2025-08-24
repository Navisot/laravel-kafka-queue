# Laravel Kafka Queue Driver

A lightweight Laravel queue connector using **Kafka** via the `ext-rdkafka` PHP extension.

## Requirements

- PHP **8.1+**
- Laravel **9+** (works with 10/11)
- **ext-rdkafka** (PECL) built against **librdkafka**

> If your app doesn’t have `ext-rdkafka` installed, see **Installing librdkafka + ext-rdkafka** below.

## Install

```bash
composer require navisot/laravel-kafka-queue
```

If you’re not using package auto-discovery, add the service provider:

```php
// config/app.php
// or bootstrap/providers.php
'providers' => [
    // ...
    Kafka\KafkaServiceProvider::class,
],
```

## Configure queue connection

Add a `kafka` connection to **config/queue.php**:

```php
'connections' => [

    // ... other connections

    'kafka' => [
        'driver'            => 'kafka',
        'queue'             => env('KAFKA_QUEUE', 'default'),
        'bootstrap_servers' => env('BOOTSTRAP_SERVERS'),
        'security_protocol' => env('SECURITY_PROTOCOL', 'SASL_SSL'),
        'sasl_mechanisms'   => env('SASL_MECHANISMS', 'PLAIN'),
        'sasl_username'     => env('SASL_USERNAME'),
        'sasl_password'     => env('SASL_PASSWORD'),
        'group_id'          => env('GROUP_ID', 'laravel-consumer'),
        'auto_offset_reset' => env('AUTO_OFFSET_RESET', 'earliest'),
    ],

],
```

And set the env variables in **.env**:

```dotenv
QUEUE_CONNECTION=kafka
KAFKA_QUEUE=my-topic

BOOTSTRAP_SERVERS=broker1:9092,broker2:9092
SECURITY_PROTOCOL=SASL_SSL
SASL_MECHANISMS=PLAIN
SASL_USERNAME=your-username
SASL_PASSWORD=your-password
GROUP_ID=my-consumer-group
AUTO_OFFSET_RESET=earliest
```

## Use

Run a worker:

```bash
php artisan queue:work kafka
```

Dispatch a job to Kafka (uses the default `KAFKA_QUEUE` unless you pass a queue name):

```php
MyJob::dispatch($payload)->onQueue('my-topic');
```

## Installing librdkafka + ext-rdkafka

Composer cannot install system libraries. You need **librdkafka** and then install **ext-rdkafka** via PECL.

### Debian/Ubuntu

```bash
sudo apt-get update
sudo apt-get install -y librdkafka-dev
sudo pecl install rdkafka
# enable extension (CLI+FPM as needed)
echo "extension=rdkafka.so" | sudo tee /etc/php/$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')/mods-available/rdkafka.ini
sudo phpenmod rdkafka
sudo service php*-fpm restart 2>/dev/null || true
```

### macOS (Homebrew)

```bash
brew install librdkafka
pecl install rdkafka
echo "extension=rdkafka.so" >> $(php --ini | awk -F': ' '/Scan for additional/{print $2}')/rdkafka.ini
```

### Docker (example)

```dockerfile
FROM php:8.2

RUN apt-get update && apt-get install -y --no-install-recommends       librdkafka-dev       libsasl2-dev       libssl-dev       unzip  && rm -rf /var/lib/apt/lists/*

RUN pecl install rdkafka  && docker-php-ext-enable rdkafka

# optional: other PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist
```