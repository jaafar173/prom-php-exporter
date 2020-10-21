<?php

namespace Lcmf\Prometheus\LaravelExporter;

use Illuminate\Contracts\ROuting\ResponseFactory;
use Illuminate\Routing\Controller;
use Prometheus\RenderTextFormat;

class MetricsController extends Controller
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var PrometheusExporter
     */
    protected $prometheusExporter;

    /**
     * MetricsController constructor.
     * @param ResponseFactory $responseFactory
     * @param PrometheusExporter $prometheusExporter
     */
    public function __construct(ResponseFactory $responseFactory,PrometheusExporter $prometheusExporter)
    {
        $this->prometheusExporter = $prometheusExporter;
        $this->responseFactory = $responseFactory;
    }


    /**
     * /metrics
     * Get Prometheus Metrics
     */
    public function getMetrics()
    {
        $metrics = $this->prometheusExporter->export();

        $render = new RenderTextFormat();
        $result = $render->render($metrics);

        $this->responseFactory->make($result, 200, ["Content-Type"=>RenderTextFormat::MIME_TYPE]);
    }
}
