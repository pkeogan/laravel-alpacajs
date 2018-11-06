<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use App\Models\Auth\Permission;
use Illuminate\Support\Facades\Auth;
use Pkeogan\LaravelAlpacaJS\Traits\ApiPermissionTrait;

/**
 * Trait build
 */
trait ReadTrait
{
	public function checkAndReadWithQuery(Request $request, $query, $model)
	{
		if(isset($query['childrenUserCan'])){
			return response()->json($model::getModelsAllowedtoDoThis($query['childrenUserCan']));
		}
		

		
		if(isset($query['pluck']))
		{
				if(isset($query['pluckKey']))
				{
					$array = $model::all()->pluck($query['pluck'], $query['pluckKey'])->toArray();
					return response()->json(array_prepend($array, "",""));
				} else {
					$array = $model::all()->pluck($query['pluck'])->toArray();
					return response()->json(array_prepend($array, "",""));
				}
		}
		
		if(isset($query['where'])){
			return response()->json($model::getModelsAllowedtoDoThis($query['childrenUserCan']));
		}
		
		//blanket catchfor where method to check model for the method and attempt to run it with given 
		foreach($query as $key=>$value)
		{
			$method = "apiAction" . $key;
			if(method_exists($model, $method))
			{
				return  response()->json($model->$method($value));
			}
		}

		
		abortJSON(404, "Action not found");
	}
	
}
