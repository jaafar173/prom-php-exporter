<?php

namespace PromPhpExporter\LaravelExporter;

use Prometheus\Counter;
use Prometheus\CollectorRegistry;
use InvalidArgumentException;
use PromPhpExporter\LaravelExporter\Contracts\CollectorInterface;

class PrometheusExporter
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var CollectorRegistry
     */
    protected $prometheus;

    /**
     * @var array
     */
    protected $collectors = [];


    public function __construct(string $namespace,CollectorRegistry $prometheus,array $collectors=[])
    {
        $this->namespace  = $namespace;
        $this->prometheus = $prometheus;

        foreach ($collectors as $collector){
            $this->registerCollector($collector);
        }
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return CollectorRegistry
     */
    public function getPrometheus()
    {
        return $this->prometheus;
    }

    /**
     * @return array
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * @param  $name
     * @return mixed
     */
    public function getCollector($name)
    {
        if(isset($this->collectors[$name])) {
            throw new InvalidArgumentException(sprintf("The name:%s not exists~", $name));
        }
        return $this->collectors[$name];
    }


    /**
     * Register Collector
     *
     * @param CollectorInterface $collector
     */
    public function registerCollector(CollectorInterface $collector)
    {
        $name = $collector->getName();
        if(!isset($this->collects[$name])) {
            $this->collectors[$name] = $collector;

            $collector->registerMetrics($this);
        }
    }

    /**
     * @param  $name
     * @param  $help
     * @param  array $labels
     * @return \Prometheus\Counter
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function registerCounter($name, $help, $labels = [])
    {
        return $this->prometheus->registerCounter($this->namespace, $name, $help, $labels);
    }

    /**
     * Return a counter.
     *
     * @param string $name
     *
     * @return \Prometheus\Counter
     */
    public function getCounter($name)
    {
        return $this->prometheus->getCounter($this->namespace, $name);
    }

    /**
     * Return or register a counter.
     *
     * @param string $name
     * @param string $help
     * @param array  $labels
     *
     * @return \Prometheus\Counter
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#counter
     */
    public function getOrRegisterCounter($name, $help, $labels = [])
    {
        return $this->prometheus->getOrRegisterCounter($this->namespace, $name, $help, $labels);
    }

    /**
     * Register a gauge.
     *
     * @param string $name
     * @param string $help
     * @param array  $labels
     *
     * @return \Prometheus\Gauge
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#gauge
     */
    public function registerGauge($name, $help, $labels = [])
    {
        return $this->prometheus->registerGauge($this->namespace, $name, $help, $labels);
    }

    /**
     * Return a gauge.
     *
     * @param string $name
     *
     * @return \Prometheus\Gauge
     */
    public function getGauge($name)
    {
        return $this->prometheus->getGauge($this->namespace, $name);
    }

    /**
     * Return or register a gauge.
     *
     * @param string $name
     * @param string $help
     * @param array  $labels
     *
     * @return \Prometheus\Gauge
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#gauge
     */
    public function getOrRegisterGauge($name, $help, $labels = [])
    {
        return $this->prometheus->getOrRegisterGauge($this->namespace, $name, $help, $labels);
    }

    /**
     * Register a histogram.
     *
     * @param string $name
     * @param string $help
     * @param array  $labels
     * @param array  $buckets
     *
     * @return \Prometheus\Histogram
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#histogram
     */
    public function registerHistogram($name, $help, $labels = [], $buckets = null)
    {
        return $this->prometheus->registerHistogram($this->namespace, $name, $help, $labels, $buckets);
    }

    /**
     * Return a histogram.
     *
     * @param string $name
     *
     * @return \Prometheus\Histogram
     */
    public function getHistogram($name)
    {
        return $this->prometheus->getHistogram($this->namespace, $name);
    }

    /**
     * Return or register a histogram.
     *
     * @param string $name
     * @param string $help
     * @param array  $labels
     * @param array  $buckets
     *
     * @return \Prometheus\Histogram
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#histogram
     */
    public function getOrRegisterHistogram($name, $help, $labels = [], $buckets = null)
    {
        return $this->prometheus->getOrRegisterHistogram($this->namespace, $name, $help, $labels, $buckets);
    }

    /**
     * Export the metrics from all collectors.
     *
     * @return \Prometheus\MetricFamilySamples[]
     */
    public function export()
    {
        foreach ($this->collectors as $collector) {
            /* @var CollectorInterface $collector */
            $collector->collect();
        }

        return $this->prometheus->getMetricFamilySamples();
    }

}