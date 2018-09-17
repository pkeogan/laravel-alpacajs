<?php

namespace Pkeogan\LaravelAlpacaJS;

use Illuminate\Support\Facades\Facade;

/**
 * @see Pkeogan\AlpacaJSFacade
 */
class AlpacaJSFacade extends Facade
{    

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alpacajs';
    }
   
  
}