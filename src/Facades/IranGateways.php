<?php

namespace MeysamZnd\IranGateways\Facades;

use Illuminate\Support\Facades\Facade;

class IranGateways extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'iran-gateways';
    }
}
