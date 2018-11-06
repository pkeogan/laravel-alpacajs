<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use App\Models\Schedule\ShiftType;


/**
 * Trait build
 */
trait ChildResponse
{

    public function respondToChild()
    {
		try{
        //check to see if incoming child has given parent info
        $childClass = class_basename(get_class($this->model));
        if(!array_key_exists ($childClass, config('alpacajs.children'))){ abortJSON(400, "Child class is not setup or entered wrong."); }
        if($this->request->route('id'))
		{
				$parent = config('alpacajs.children.' . $childClass . '.parent.location')::findByUuid($this->request->route('id'));
		} 
		if($this->request->has(config('alpacajs.children.' . $childClass . '.parent.identifier')))
		{
			if(config('alpacajs.children.' . $childClass . '.parent.uuid', false))
			{
				$parent = config('alpacajs.children.' . $childClass . '.parent.location')::findByUuid($this->request[config('alpacajs.children.' . $childClass . '.parent.identifier')]);

			} else {
				$parent = config('alpacajs.children.' . $childClass . '.parent.location')::find($this->request[config('alpacajs.children.' . $childClass . '.parent.identifier')]);
			}
		} else {
			 abortJSON(400, "A parent wasnt given via url or post request");
		}
			
      		
        if(!$parent){abortJSON(404, "unable able to locate parent with given info");}
        //parent was found!

            // Client is attemtping to save/store data
        if ($this->request->isMethod('post')) {
            if ($this->action == "create") {
                $this->storeChildModel($parent);
            } elseif ($this->action == "edit") {
                $this->updateChildModel($parent);
            } elseif ($this->action == "self-assign") {
                $this->selfAssignChildModel($parent);
            } elseif ($this->action == "add") {
                $this->addChildModel($parent);
            } elseif ($this->action == "delete") {
                $this->deleteChildModels($parent);
            } elseif ($this->action == "clone") {
                $this->cloneChildModels($parent);
            } else {
                throw new \Exception('Unxepceted Action Type');
                abortJSON(400, 'Unxepceted Action Type Attempted');
            }
        }
        // Client is attemtping to get data
        elseif ($this->request->isMethod('get')) {
            if ($this->action == "create") {
                abortJSON(501, 'Return some preset data for created');
            } elseif ($this->action == "edit") {
                return $this->respondWithChildData($parent);
            } else {
                throw new \Exception('Unxepceted Action Type');
                abortJSON(400, 'Unxepceted Action Type Attempted');
            }
        } else {
            abortJSON(405, 'This method is not allowed.');
        }
		} catch  (Exception $e) { 
			abortJSON(500, 'There was an error: ERROR CODE 56:' . $e);
		}

    }
	
	private function createChildModel($parent)
	{
		if(is_null($parent->config('validate-create'))){abortJSON(500, 'Server Valdiation Missing');}
		$validator = validator($this->request->all(), json_decode($parent->config('validate-create'), true));
		if (!$validator->errors()->isEmpty()) {
			abortJSON(400, $validator->errors());
		}
		try{
			$this->model->createAPI($validator->getData());
			 abortJSON(200, 'Request has been processed with no errors.');
		} catch  (Exception $e) { 
			abortJSON(500, 'There was an error saving the data:' . $e);
		}
	}

	private function storeChildModel($parent)
	{
        $this->checkModelPermission($parent, "create");
		$this->createChildModel($parent);
	}
	
		private function selfAssignChildModel($parent)
		{
            $this->checkModelPermission($parent, "self-assign");
			
			if(!isset($this->request['user_id']))
			{
				$this->request['user_id'] = strval(auth()->user()->uuid);
			}
			if($this->request['user_id'] != auth()->user()->uuid)
			{
				abortJSON(400, 'You attempted to self assign to another user');
			} 
			$this->createChildModel($parent);

		}
	
	private function addChildModel($parent)
	{
        $this->checkModelPermission($parent, "add");
		$this->createChildModel($parent);

	}

	private function updateChildModel($parent)
	{
		$this->checkPermission('edit');
		$validator = validator($this->request->all(), json_decode($parent->config('validate-edit'), true));
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
    
    private function checkModelPermission($parent, $action_name)
	{
		if(!auth()->check()){abortJSON(401, 'You are not authenticated');}
		if(!$parent->canDoAction($action_name))
		{
			abortJSON(403, 'You are not allowed to make such a request.');
		}
	}

}