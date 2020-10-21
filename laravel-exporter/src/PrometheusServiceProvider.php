<?php

namespace Lcmf\Prometheus\LaravelExporter;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Storage\Adapter;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者呗注册后调用
     */
    public function boot()
    {
        $this->publishes(
            [
            __DIR__."/../config/prometheus.php" => config_path("prometheus.php")
            ]
        );

        if(config("prometheus.metrics_route_enabled")) {
            $this->loadRoutesFrom(__DIR__."/Routes/routes.php");
        }
        $exporter = $this->app->make(PrometheusExporter::class);

        foreach (config("prometheus.collectors") as $collector){
            $exporter->registerCollector($collector);
        }
    }

    /**
     * 在服务容器里注册
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__."/../config/prometheus.php", "prometheus");

        $this->app->singleton(
            PrometheusExporter::class, function ($app) {
                $storageAdapter = $app["prometheus.storage_adapter"];
                $prometheus     = new CollectorRegistry($storageAdapter);
                return new PrometheusExporter(config("prometheus.namespace"), $prometheus);
            }
        );

        $this->app->alias(PrometheusExporter::class, "prometheus");

        $this->app->bind(
            "prometheus.storage_adapter_factory", function () {
                return new StorageAdapterFactory();
            }
        );

        $this->app->bind(
            Adapter::class, function ($app) {
                $storageAdapterFactory = $app->make("prometheus.storage_adapter_factory");
                $driverName            = config("prometheus.storage_adapter");
                $driverOptions         = config("prometheus.storage_adapter_options");
                $driverOption          = Arr::get($driverOptions, $driverName, []);

                return $storageAdapterFactory($driverName, $driverOption);
            }
        );
        $this->app->alias(Adapter::class, "prometheus.storage_adapter");
    }

    /**
     * 延迟提供
     *
     * @return array|string[]
     */
    public function provides()
    {
        return ["prometheus","prometheus.storage_adapter_factory","prometheus.storage_adapter"];
    }
}