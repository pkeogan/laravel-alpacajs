<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use App\Models\Auth\Permission;
/**
 * Trait build
 */
trait ApiChild 
{
	use ApiTrait;
	protected $is_child = true;
	protected $is_parent = false;
	
		
	//checks, vaildates, and then creates and returns a model. False given if ever gone wrong.
	public function checkValidateAndCreateChild(Request $request)
	{
		dd('child make');
	}
	
}