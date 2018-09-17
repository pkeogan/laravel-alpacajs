<?php

namespace Pkeogan\LaravelAlpacaJS;

use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use App\Exceptions\GeneralException;
use Pkeogan\LaravelAlpacaJS\Traits\Build;
use Pkeogan\LaravelAlpacaJS\Traits\Response;
use Pkeogan\LaravelAlpacaJS\Traits\ChildResponse;


/**
 * Class AlpacaJS
 */
class AlpacaJS
{    
	use Response, ChildResponse;
  
    protected $type = null;
    protected $view;

    protected $types;
    protected $types_text;
    //vars for holding fields that were made
    protected $data;

    //vars for building feilds
    protected $name;
    protected $field;
  	protected $schema;
  	protected $options;
    protected $sourceView;

    //vars for building responses
    protected $model;
    protected $action;
    protected $request;

    protected $config;

  
    public function __construct (Factory $view) {
      $this->view = $view;
      $this->config;
    }

  
  public function __call($name, $input)
    {
          $input = $input[0];
          //$method = substr($name, 0, 4);
          //parameter = strtolower(substr($name, 4, 1)) . substr($name, 5);
      
          if(in_array($name, config('alpacajs.fields'))){
             $this->data['field'] = $input;
          } elseif(in_array($name, array_keys(config('alpacajs.schema')))){
              $this->schema[$name] = $input;
          } elseif(in_array($name, array_keys(config('alpacajs.options')))){
              $this->options[$name] = $input;
          } else {
            throw new \Exception('Magic Method could not locate target in config.... var=' . $name);
          }
          return $this; // Continue The Chain
      }
  
  
    public function __get($input)
    {
        //$method = substr($value, 0, 4);
        //$parameter = substr($value, 4);
          if(in_array('boolean', array_values(config('alpacajs.schema'))) && in_array($input, array_keys(config('alpacajs.schema')))){
              $this->schema[$input] = true;
          } elseif(in_array('boolean', array_values(config('alpacajs.options'))) && in_array($input, array_keys(config('alpacajs.options')))){
              $this->options[$input] = true;
          } elseif( in_array($input, config('alpacajs.boolean')) ){
              $this->data[$input] = true;
          } else {
            throw new \Exception('Magic Method could not locate target in config.... var=' . $input);
          }
          return $this; // Continue The Chain
    }
  
    public function raw($input)
    {
        $raw = new Raw($input);
        return $raw;
    }

  public function schema($input)
	{
		$this->data['schema'] = Encoder::encode($input);
		return $this;
	}

	public function options($input)
	{
		$this->data['options'] = Encoder::encode($input);
		return $this;
	}
	

  public function dataRoute($input)
  {
    $this->data['dataRoute'] = $input;
    return $this;
  }
	
	public function dataSource($input)
  {
    $this->data['dataSource'] = $input;
    return $this;
  }
  
  public function postRender($input)
  {
    $this->data['postRender'] = $input;

    return $this;
  }

	public function schemaSource($input)
  {
    $this->data['schemaSource'] = $input;
    return $this;
  }
	public function optionsSource($input)
  {
    $this->data['optionsSource'] = $input;
    return $this;
  }
	public function viewSource($input)
  {
    $this->data['viewSource'] = $input;
    return $this;
  }
	
   public function route($input)
  {
    $this->data['route'] = $input;
    return $this;
  }
	
  public function link($input)
  {
    $this->data['link'] = $input;
    return $this;
  }

  public function modal()
  {
    $this->type = 'modal';
    return $this;
  }

  public function set($key, $value)
  {
      array_set($this->data, $key, $value);
      return $this;
  }

  public function create($route)
  {
    $this->type = 'modal';
    $this->data = config('alpacajs.create');
    $this->data['storeRoute'] = route($route, 'create');
    return $this;
  }

  public function edit($route)
  {
    $this->type = 'modal';
    $this->data = config('alpacajs.edit');
    $this->data['dataRoute'] = route($route, 'edit');
    $this->data['updateRoute'] = route($route, 'edit');
    return $this;
  }

  public function display()
  {
    $this->type = 'modal';
    $this->data = config('alpacajs.display');
    return $this;
  }
	
  public function form()
  {
    $this->type = 'form';
    return $this;
  }
  
  public function name(String $input)
  {
    $this->data['name'] = $input;
    return $this;
  }
  
  public function id(String $input)
  {
    $this->data['id']= $input;
    return $this;
  }
  
  
  public function value($input)
  {
    $this->data['value'] = $input;
    return $this;
  }
  
  public function attributes(Array $input)
  {
    if(is_null($this->data['attributes'])){
      $this->data['attributes'] = $input;
    } else {
      $this->data['attributes'] = array_merge($this->data['attributes'], $input);
    }
    return $this;
  }
  
  public function data($input)
  {
  	if(is_null($this->data['data'])){
      $this->data['data'] = $input;
    } else {
      $this->data['data'] = array_merge($this->data['data'], $input);
    }
    return $this;
    $this->data['data'] = $input;
    return $this;
  }

  
  public function compile()
  {
        if(! isset($this->data['name'])){ throw new \Exception('ERROR: NAME WASNT SET '); };
        if(! isset($this->data['id'])){ $this->data['id'] = str_replace(' ', '_', strtolower($this->data['name'])); }; //if the ID wasnt set, set it to the $this->name alpah-underscore lower case
 }

  
  public function render()
  {
        $this->compile();
        $type = $this->type;
        $data = $this->data;
        $this->type = null;
      //reset for next render call.
        $this->data = ['name' => null,
                    'id' => null,
                    'helper_text' => null,
                    'value' => null,
                    'attributes' => null,
                    'data' => null];
    
        return $this->renderComponent($type, $data);
  }
	
public function dd()
  {
        $this->compile();
        $type = $this->type;
        $data = $this->data;
        $this->type = null;
      //reset for next render call.
        $this->data = ['name' => null,
                    'id' => null,
                    'helper_text' => null,
                    'value' => null,
                    'attributes' => null,
                    'data' => null];
    
		dd($data);
  }
  
  
    /**
     * Transform the string to an Html serializable object
     *
     * @param $html
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function toHtmlString($html)
    {
        return new HtmlString($html);
    }
  
    /**
     * Render Component
     *
     * @param        $name
     * @param  array $arguments
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function renderComponent($type, $data)
    {
        return new HtmlString(
          $this->view->make('alpacajs::' . $type, $data)->render()
        );
    }
  
  
}