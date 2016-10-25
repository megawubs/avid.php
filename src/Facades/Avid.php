<?php
namespace Wubs\Avid\Facades;

use Illuminate\Support\Facades\Facade;

class Avid extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'avid';
    }
}
