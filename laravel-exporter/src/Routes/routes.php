<?php

use Illuminate\Support\Facades\Route;

//Version;
$route = Route::get(config('prometheus.metrics_route_path'),PromPhpExporter\LaravelExporter\MetricsController::class."@getMetrics");

