<?php

namespace Pkeogan\LaravelAlpacaJS\Models\Fields;

use Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use App\Exceptions\GeneralException;

/**
 * Trait build
 */
abstract class AbstractFieldModel implements 
{    
	protected $name;
	protected $schema;
	protected $options;
	
	public function build()
	{
		return $this;
	}
	
	public function name(string $input)
	{
		$this->name = $input;
		return $this;
	}

	public function schema(array $input)
	{
		$this->schema = $input;
		return $this;
	}
	
	public function options(array $input)
	{
		$this->options = $input;
		return $this;
	}
	
	public function validateText($input)
	{
		return  Validator::make($input, [
			'name' => 'string|required',
            'schema.default' => 'nullable',
			'schema.title' => 'sometimes|string',
            'schema.type' => 'string|required',
			'schema.required' => 'sometimes|boolean',
        ]);
	}
	
	public function render()
	{
		return ['name' => $this->name,
			   'schema' => $this->schema,
			   'options' => $this->options ];
	}
	
}