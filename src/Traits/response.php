<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use App\Models\Auth\Permission;
use \Ramsey\Uuid\Uuid;
/**
 * Trait build
 */
 
trait Response
{
    public function build()
    {
        return $this;
    }

    public function model($input)
    {
        $this->model = $input;
        return $this;
    }

    public function action($input)
    {
        $this->action = $input;
        return $this;
    }

    public function request($input)
    {
        $this->request = $input;
        return $this;
    }

    public function respond()
    {
		//check if incoming response is identifies as a child of a model. if so, we are going to have look at the parent model for instructions.
		if($this->request->has("isChildModel"))
		{   //child id found, lets go find out what we need to do
			return $this->respondToChild();
		}


        // Client is attemtping to save/store data
        if ($this->request->isMethod('post')) {
            if ($this->action == "create") {
                $this->storeModel();
            } elseif ($this->action == "edit") {
                $this->updateModel();
            } elseif ($this->action == "delete") {
                $this->deleteModels();
            } elseif ($this->action == "clone") {
                $this->cloneModels();
            } else {
                throw new \Exception('Unxepceted Action Type');
                abortJSON(400, 'Unxepceted Action Type Attempted');
            }
        }
        // Client is attemtping to get data
        elseif ($this->request->isMethod('get')) {
			if ($this->action == "create") {
				abortJSON(501, 'Return some preset data for created');
			} elseif ($this->action == "edit") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithData();
			} elseif ($this->action == "schema") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithSchema();
			} elseif ($this->action == "options") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithOptions();
			} elseif ($this->action == "post-render") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithPostRender();
			} elseif ($this->action == "users") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithUsers();
			} elseif ($this->action == "data") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithData();
			} elseif ($this->action == "log") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithLog();
			} elseif ($this->action == "view-all") { //actually the view action below but keeping to prevent breaks
				return $this->respondWithViewAll();
			} elseif ($this->action == "view") { 
				return $this->respondWithData();
			} else {
				throw new \Exception('Unxepceted Action Type');
				abortJSON(400, 'Unxepceted Action Type Attempted');
			}
        } else {
            abortJSON(405, 'This method is not allowed.');
        }
	}

	private function storeModel()
	{
		$this->checkPermission('create');
		
		$validator = validator($this->request->all(), $this->model->rulesApi('create'));
		if (!$validator->errors()->isEmpty()) {
			abortJSON(400, $validator->errors());
		}
		if($this->request->route('id') != null)
		{
			abortJSON(400, 'You are attempting to create a resoruce on top of a model');
		}
		try{
			 $this->model->createAPI($validator->getData());
			 abortJSON(200, 'Request has been processed with no errors.');
		} catch  (Exception $e) { 
			abortJSON(500, 'There was an error saving the data:' . $e);
		}
	}

	private function updateModel()
	{
		$this->checkPermission('edit');
		$validator = validator(json_decode($this->request->getContent(), true), $this->model->rulesApi('edit'));
		if (!$validator->errors()->isEmpty()) {
			abortJSON(400, $validator->errors());
		}
		if($this->request->route('id') == null){
			abortJSON(400, 'You are attempting to update something that doesnt exist');
		}
		try{
			 $this->model->updateAPI($validator->getData());
			 abortJSON(200, 'Request has been processed with no errors.');
		} catch (Exception $e) { 
			abortJSON(500, 'There was an error updating the data:' . $e);
		}
	}

	 private function cloneModels()
    {
		$cloned = 0;
		$kept = 0;
		foreach($this->request['uuids'] as $uuid)
		{
			$model =  $this->model::findByUuid($uuid);
			$clone = $this->model->replicate();
			$clone->fill($model->toArray());
			$clone->name = $clone->name ." (Copy:" . str_random(5) . ")";
			if ($clone->push()) {
					$cloned++;
				} else {
					$kept++;
				}
		}
		if($kept == 0)
		{			
			abortJSON(200, $cloned . ' out of ' . $cloned . ' requested clones were made.');

		} else {
			abortJSON(500, 'There was an error cloning the selected resource(s): ' . $kept . ' out of ' . $kept + $cloned . ' were cloned.');
		}
	}

	 private function deleteModels()
    {
		$deleted = 0;
		$kept = 0;
		foreach($this->request['uuids'] as $uuid)
		{
			$model =  $this->model::findByUuid($uuid);
			if ($model->delete()) {
					$deleted++;
				} else {
					$kept++;
				}
		}
		if($kept == 0)
		{			
			abortJSON(200, $deleted . ' out of ' . $deleted . ' requested deletions were made.');

		} else {
			abortJSON(500, 'There was an error deleting the selected resource(s): ' . $kept . ' out of ' . $kept + $deleted . ' were deleted.');
		}
	}

	private function checkPermission($action_name)
	{
		if(!auth()->check()){abortJSON(401, 'You are not authenticated');}
		if($this->model->id != null && config('alpacajs.children.' . class_basename($this->model), false) != false)
		{
			if(!$this->model->parent->canModel($action_name))
			{
				abortJSON(403, 'You are not allowed to make such a request.(VIA Parent Model)');
			}
		}
		elseif(!$this->model::staticCan($action_name))
		{
			abortJSON(403, 'You are not allowed to make such a request.');
		}
	}
	
	private function permDestroyModel()
    {
		$this->checkPermission('delete');
		abortJSON(501, 'Function not built yet');

	}
	
	private function duplicateModel()
    {
		$this->checkPermission('clone');
		 abortJSON(501, 'Function not built yet');
    }
	
	private function respondWithUsers()
    {
		if(!$this->model){abortJSON(404, 'The resource you are looking for cannot be found.');}
		if($this->model instanceof Permission)
		{
			$users = Permission::getUsersFromPermission($this->model->name)->pluck('name', 'uuid')->toArray();			
		} else {
			$users = $this->model->users->pluck("name", "uuid")->toArray();
		}
			
        return response()->json($users);
    }
	
	private function respondWithViewAll()
    {
		$models = $this->model::all()->pluck('name', 'uuid')->toArray();
        return response()->json($models);
    }

    private function respondWithData()
    {

		//can user even get data on this model?
		$this->checkPermission('view');

		$id = $this->request->route('id');
		$var1 = $this->request->route('var1');
		$var2 = $this->request->route('var2');

		//check if a model in the API is being called on
		if($id == 'all')
		{
			//user didnt speciify a model, user must be looking for generic data about the model, or from the config
			
			if($var1 =='get-models-user-can')
			{
				if($var2 != null)
				{
					return response()->json($this->model::getModelsAllowedtoDoThis($var2));
				} else {
					abortJSON(400, 'RESTAPI ERROR: var 2 wasnt set');
				}
			}
			
			if($var1 =='get-for-select')
			{
				return response()->json($this->model::all()->pluck('name', 'uuid')->toArray());
			}
			
			
					
			abortJSON(501, 'Function not built yet');

			
		} else {

			//user is looking for data about the model specifed
			
			if($var1 != null) {
				if($var1 == "config-value-to-json"){ //user wants something from the model's config
					if($var2 != null){ //if var 2 if is null, the user wants it all
						return response()->json( array_mirror_values( explode(",", $this->model->config($var2)) ) );
					} else {
						abortJSON(400, 'RESTAPI ERROR: value was set');
					}
				}
				if($var1 == "event-details")
				{
					return response()->json( $this->model->eventDetails );
				}
				
				abortJSON(501, 'Function not built yet');
			} else {
				//no extra vars specifed, user must want all the data, 
				return response()->json($this->model->dataAPI());
			}     
			
		}
		
       abortJSON(501, 'Function not built yet');

    }

    private function respondWithSchema()
    {	
		$json = ['type' => 'object', 'properties' => null];
		$class = model_class_name($this->model);
		//check if model given in a child model. if it is we need to reset the model we are attempting to grab things from and we nee
		if($this->request->route('id') && $this->model->id == null && config('alpacajs.children.' . $class, false))
		{
			$tempModel =  config('alpacajs.children.' . $class. '.parent.location')::findByUuid($this->request->route('id') );
			if($tempModel != null)
			{
				$this->model = $tempModel;
				$class = model_class_name($this->model);
			}
		}
		if(config('alpacajs.children.' . $class, false) == true)
		{
			$default = config('alpacajs.parent.' . config('alpacajs.children.' . $class . '.parent.class') . '.schema', array());
		} else {
			$default = config('alpacajs.parent.' . $class . '.schema', array());
			$json['properties'] = $default;
			return response()->json($json);
		}		
		if($this->request->route('id') == null || !isset($this->model->data['schema'])){ //no model given, return the default schema
			$json['properties'] = $default;
			return response()->json($json);
		}
		
		
	 	$json['properties'] = array_merge($default, $this->model->data['schema']);
		
		if($this->request->route('var1') == "remove"){
			if($this->request->route('var2') != null)
			{
				$removes = explode("-", $this->request->route('var2'));
				foreach($removes as $remove)
				{
					array_forget($json, 'properties.' . $remove);
				}
			} else {
				return 	abortJSON(400, 'RESTAPI: fields to remove were not given');
			}
			
		}
		
		return response()->json($json);
    }

    private function respondWithOptions()
    {

		$json = ['fields' => null];
		$class = model_class_name($this->model);
		//check if model given in a child model. if it is we need to reset the model we are attempting to grab things from and we nee
		if($this->request->route('id') && $this->model->id == null && config('alpacajs.children.' . $class, false))
		{
			$tempModel =  config('alpacajs.children.' . $class. '.parent.location')::findByUuid($this->request->route('id') );
			if($tempModel != null)
			{
				$this->model = $tempModel;
				$class = model_class_name($this->model);
			}
		}
		if(config('alpacajs.children.' . $class, false) == true)
		{
			$default = config('alpacajs.parent.' . config('alpacajs.children.' . $class . '.parent.class') . '.options', array());
		} else {
			$default = config('alpacajs.parent.' . $class . '.options', array());
			$json['fields'] = $default;
			return response()->json($json);
		}
		if($this->request->route('id') == null || !isset($this->model->data['options'])){ //no model given or model doesnt have options to select, return the default options
			$json['fields'] = $default;
			return response()->json($json);
		}
	
		
	 	$json['fields'] = array_merge($default, $this->model->data['options']);
		
		if($this->request->route('var1') == "remove"){
			if($this->request->route('var2') != null)
			{
				$removes = explode("-", $this->request->route('var2'));
				foreach($removes as $remove)
				{
					array_forget($json, 'fields.' . $remove);
				}
			} else {
				return 	abortJSON(400, 'RESTAPI: fields to remove were not given');
			}
			
		} elseif($this->request->route('var1') == "hide"){
			if($this->request->route('var2') != null)
			{
				$removes = explode("-", $this->request->route('var2'));
				foreach($removes as $remove)
				{
					array_set($json, 'fields.' . $remove . '.type', "hideen");
				}
			} else {
				return 	abortJSON(400, 'RESTAPI: fields to remove were not given');
			}
			
		}
		
		return response()->json($json);
	} 
	
	
	
	private function respondWithPostRender()
     {
		$class = model_class_name($this->model);
		//check if model given in a child model. if it is we need to reset the model we are attempting to grab things from and we nee
		if($this->request->route('id') && $this->model->id == null && config('alpacajs.children.' . $class, false))
		{
			$tempModel =  config('alpacajs.children.' . $class. '.parent.location')::findByUuid($this->request->route('id') );
			if($tempModel != null)
			{
				$this->model = $tempModel;
				$class = model_class_name($this->model);
			}
		}
		if(config('alpacajs.children.' . $class, false) == true)
		{
			$postRender = str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', config('alpacajs.parent.' . config('alpacajs.children.' . $class . '.parent.class') . '.postRender', null));
		} else {
			$postRender = str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', config('alpacajs.parent.' . $class . '.postRender', null));
		}
		if($this->request->route('id') == null || !isset($this->model->data['postRender'])){  //no model given, return the default postRender 
		 return $postRender;
		}  
		
		if(isset($this->model->data['postRender']))
		{
			$postRender .=  str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', $this->model->data['postRender']);
		}

		if($this->request->route('var1') == "remove"){
			if($this->request->route('var2') != null)
			{
				$removes = explode("-", $this->request->route('var2'));
				foreach($removes as $remove)
				{			
					$start = "/*" . $remove . "*/";
					$end = "/*!" . $remove . "*/";
					$postRender = delete_all_between($start, $end, $postRender);
				}
			} else {
				return 	abortJSON(400, 'RESTAPI: fields to remove were not given');
			}
			
		}
		
		return $postRender;
	}
	
	
	private function  respondWithLog()
	{
		if($this->model->id == null)
		{
			//return log about eerything
			return null;
		} else {
			//return log about model
			$json = array();
			$activites = $this->model->activity()->get();
			$previous = false;
			foreach($activites as $key=>$activity)
			{
				if($key <= 0)
				{
				$data = array_dot($activity->properties->toArray());
				$json[$key]['data'] = array_implode_with_keys( $data, ', ', ' = ');
				$json[$key]['changed'] = null;
				} elseif($key >= 1)
				{
				$data = array_dot($activity->properties->toArray());
				$json[$key]['data'] = array_implode_with_keys( $data, ', ', ' = ');
				$changes = array_diff(array_dot($activity->properties->toArray()), array_dot($previous->properties->toArray()));
				$json[$key]['changed'] = array_implode_with_keys( $changes, ', ', ' = ');
				}
				$json[$key]['subjectid'] = $activity->subject_id;
				$json[$key]['subjectType'] = class_basename($activity->subject_type);
				$json[$key]['type'] = $activity->description;
				$json[$key]['causer'] = $activity->causer->nameWithProfileLink;
				$json[$key]['at'] = $activity->created_at->format('m/d/Y H:i:s');
				$previous = $activity;
			}
			return response()->json(['data' => $json]);
		}
	}
	
    private function respondWithView()
    {
        return response()->json($this->viewSource);
    }

}
