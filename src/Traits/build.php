<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use App\Exceptions\GeneralException;

/**
 * Trait build
 */
 
trait Build
{    
	public function build()
	{
		return $this;
	}
		
	public function schema($input)
	{
		$this->schema = $input;
		return $this;
	}

	public function options($input)
	{
		$this->options = $input;
		return $this;
	}
	
}