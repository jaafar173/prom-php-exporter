<?php

namespace PromPhpExporter\LaravelExporter;

use Illuminate\Support\Facades\Facade;

class  PrometheusFacade extends Facade
{

    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'prometheus';
    }
}
