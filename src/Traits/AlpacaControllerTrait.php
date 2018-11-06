<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use App\Models\Auth\Permission;
/**
 * Trait AlpacaControllerTrait
 */
trait AlpacaControllerTrait
{
	
	public function alpacaButtons(Request $request)
    {	
		//$this->model->canUserDoTheseActions(['view']);
		return $this->model->getButtons();

    }
	
	public function alpacaSchema(Request $request)
    {	
		$this->model->canUserDoTheseActions(['create', 'edit']);
		$json = array();
		if(isset($this->query['edit']) && $this->query['edit'])
	   	{
			$json = ['type' => 'object', 'properties' => $this->model->getClassSchema()];
	   	}
		elseif($schema = $this->model->getSchema())
		{
			$json = ['type' => 'object', 'properties' => $schema];
		} else {
			return 	abortJSON(400, 'Model you are asking for doesnt have a schema setup');
		}

		//Check the query for commands
		if(isset($this->query['remove'])){
			if(is_array($removes = $this->query['remove'])){
				foreach($removes as $remove){
					array_forget($json, 'properties.' . $remove);
				}
			} else {
				array_forget($json, 'properties.' . $removes);
			}
		}
		
		//check for keys to bring back below properties
		if(isset($json['properties']['--default']))
		{
			dd('deat2');
		}

			
		return response()->json($json);
		
    }
	
    public function alpacaOptions(Request $request)
    {	
		
		$this->model->canUserDoTheseActions(['create', 'edit']);
		$json = array();
		if(isset($this->query['edit']) && $this->query['edit'])
	   	{
			$json = ['fields' => $this->model->getClassOptions()];
	   	}
		elseif($schema = $this->model->getOptions())
		{
			$json = ['fields' => $schema];
		} else {
			return 	abortJSON(400, 'Model you are asking for doesnt have options setup');
		}

		//Check the query for commands
		if(isset($this->query['remove'])){
			if(is_array($removes = $this->query['remove'])){
				foreach($removes as $remove){
					array_forget($json, 'fields.' . $remove);
				}
			} else {
				array_forget($json, 'fields.' . $removes);
			}
		}
		
		return response()->json($json);

	} 
	
	
	
	public function alpacaPostRender(Request $request)
     {

		$this->model->canUserDoTheseActions(['create', 'edit']);
		$postRender = '';
		if(isset($this->query['edit']) && $this->query['edit'])
	   	{
			$postRender = $this->model->getClassPostRender();
	   	}
		elseif(!$postRender = $this->model->getPostRender()){
			return 	abortJSON(400, 'Model you are asking for doesnt have a post render setup');
		}

		//Check the query for commands
		if(isset($this->query['remove'])){
			if(is_array($removes = $this->query['remove'])){
				foreach($removes as $remove){
					$start = "/*" . $remove . "*/";
					$end = "/*!" . $remove . "*/";
					$postRender = delete_all_between($start, $end, $postRender);
				}
			} else {
					$start = "/*" . $removes . "*/";
					$end = "/*!" . $removes . "*/";
					$postRender = delete_all_between($start, $end, $postRender);
			}
		}
		
		return $postRender;
		

	}
}