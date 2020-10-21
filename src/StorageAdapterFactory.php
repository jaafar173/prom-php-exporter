<?php
namespace Lcmf\Prometheus\LaravelExporter;

use InvalidArgumentException;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;

class StorageAdapterFactory
{

    protected static $supportDriverMap = [
        "memory",
        "redis",
    ];

    /**
     * build a storage driver
     *
     * @param  $driverName
     * @param  array $options
     * @return Adapter
     */
    public function build($driverName,$options=[])
    {
        if(!in_array($driverName, self::$supportDriverMap, true)) {
            throw new InvalidArgumentException(sprintf("Don't Support Driver:%s", $driverName));
        }
        return $this->$driverName($options);
    }

    /**
     * new prometheus redis object
     *
     * @param  array $options
     * @return Redis
     */
    public function redis($options=[])
    {
        if(empty($options)) {
            throw new InvalidArgumentException("Redis Config Empty");
        }

        if(isset($options["prefix"])) {
            Redis::setPrefix($options["prefix"]);
        }
        return new Redis($options);
    }

    /**
     * new prometheus memory object
     *
     * @param  array $options
     * @return InMemory
     */
    public function memory($options=[])
    {
        return new InMemory();
    }
}