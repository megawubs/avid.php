<?php
namespace MegaWubs\Avid\Facades;

use Illuminate\Support\Facades\Facade;

class Ardent extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \MegaWubs\Avid\Avid::class;
    }
}
