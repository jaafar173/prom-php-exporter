<?php

namespace PromPhpExporter\LaravelExporter\Contracts;

use PromPhpExporter\LaravelExporter\PrometheusExporter;
interface CollectorInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param  PrometheusExporter $exporter
     * @return mixed
     */
    public function registerMetrics(PrometheusExporter $exporter);

    /**
     * @return mixed
     */
    public function collect();
}

