# prometheus-laravel-exporter

A prometheus exporter for Laravel.


## Installation

```bash
composer require lcmf/promethus
```

Register the service provider in app.php
```php
'providers' => [
    // ...
    Lcmf\Prometheus\LaravelExporter\PrometheusServiceProvider::class,
]
```

Register the facade in app.php
```php
'aliases' => [
    // ...
    'Prometheus' => Lcmf\Prometheus\LaravelExporter\PrometheusFacade::class,
]
```

## Configuration

The package has a default configuration which uses the following environment variables.
```
PROMETHEUS_NAMESPACE=app

PROMETHEUS_METRICS_ROUTE_ENABLED=true
PROMETHEUS_METRICS_ROUTE_PATH=metrics
PROMETHEUS_METRICS_ROUTE_MIDDLEWARE=null

PROMETHEUS_STORAGE_ADAPTER=memory #or redis

PROMETHEUS_REDIS_HOST=localhost
PROMETHEUS_REDIS_PORT=6379
PROMETHEUS_REDIS_DATABASE=1
PROMETHEUS_REDIS_PASSWORD=xxxx
PROMETHEUS_REDIS_PREFIX=PROMETHEUS_
```

## Usage

```php
$exporter = app('prometheus');


// export all metrics
// this is called automatically when the /metrics end-point is hit
var_dump($exporter->export());

// the following methods can be used to create and interact with counters, gauges and histograms directly
// these methods will typically be called by collectors, but can be used to register any custom metrics directly,
// without the need of a collector

// create a counter
$counter = $exporter->registerCounter('search_requests_total', 'The total number of search requests.');
$counter->inc(); // increment by 1
$counter->incBy(2);

// create a counter (with labels)
$counter = $exporter->registerCounter('search_requests_total', 'The total number of search requests.', ['request_type']);
$counter->inc(['GET']); // increment by 1
$counter->incBy(2, ['GET']);

// retrieve a counter
$counter = $exporter->getCounter('search_requests_total');

// create a gauge
$gauge = $exporter->registerGauge('users_online_total', 'The total number of users online.');
$gauge->inc(); // increment by 1
$gauge->incBy(2);
$gauge->dec(); // decrement by 1
$gauge->decBy(2);
$gauge->set(36);

// create a gauge (with labels)
$gauge = $exporter->registerGauge('users_online_total', 'The total number of users online.', ['group']);
$gauge->inc(['staff']); // increment by 1
$gauge->incBy(2, ['staff']);
$gauge->dec(['staff']); // decrement by 1
$gauge->decBy(2, ['staff']);
$gauge->set(36, ['staff']);

// retrieve a gauge
$counter = $exporter->getGauge('users_online_total');

// create a histogram
$histogram = $exporter->registerHistogram(
    'response_time_seconds',
    'The response time of a request.',
    [],
    [0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0]
);
// the buckets must be in asc order
// if buckets aren't specified, the default 0.005, 0.01, 0.025, 0.05, 0.075, 0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0 buckets will be used
$histogram->observe(5.0);

// create a histogram (with labels)
$histogram = $exporter->registerHistogram(
    'response_time_seconds',
    'The response time of a request.',
    ['request_type'],
    [0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0]
);
// the buckets must be in asc order
// if buckets aren't specified, the default 0.005, 0.01, 0.025, 0.05, 0.075, 0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0 buckets will be used
$histogram->observe(5.0, ['GET']);

// retrieve a histogram
$counter = $exporter->getHistogram('response_time_seconds');
```
