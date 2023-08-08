<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Facades;

use Illuminate\Support\Facades\Facade;

class KoiosApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'KoiosApi';
    }
}
