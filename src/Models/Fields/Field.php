<?php

namespace Pkeogan\LaravelAlpacaJS\Models\Fields;

use Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use App\Exceptions\GeneralException;
use Pkeogan\LaravelAlpacaJS\Traits\Build;
use Pkeogan\LaravelAlpacaJS\Traits\Response;
/**
 * Model Field
 */
class Field 
{    
    	use Build, 
        Response;
        
     public function __call($name, $input)
    {
          $input = $input[0];
          //$method = substr($name, 0, 4);
          //parameter = strtolower(substr($name, 4, 1)) . substr($name, 5);
      
          if(in_array($name, config('alpacajs.fields'))){
            $this->data['field'] = $input;
          } elseif(in_array($name, array_keys(config('alpacajs.schema')))){
              $this->data['schema'][$name] = $input;
          } elseif(in_array($name, array_keys(config('alpacajs.options')))){
              $this->data['options'][$name] = $input;
          } else {
            throw new \Exception('Magic Method could not locate target in config');
          }
          return $this; // Continue The Chain
      }
  
  
    public function __get($input)
    {
        //$method = substr($value, 0, 4);
        //$parameter = substr($value, 4);
          if(in_array('boolean', array_values(config('alpacajs.schema'))) && in_array($input, array_keys(config('alpacajs.schema')))){
              $this->data['schema'][$input] = true;
          } elseif(in_array('boolean', array_values(config('alpacajs.options'))) && in_array($input, array_keys(config('alpacajs.options')))){
              $this->data['options'][$input] = true;
          } else {
            throw new \Exception('Magic Method could not locate target in config');
          }
          return $this; // Continue The Chain
    }
	
}