<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use App\Models\Auth\Permission;
use Illuminate\Support\Facades\Auth;

/**
 * Trait build
 */
trait ApiPermissionTrait
{

	//check if a model can do the action directly on a model
    public function canUserDoThisActionDirectly($action_name)
    {
        if (!auth()->check()) {return abort(401, 'You are not authenticated');}
		if(isset($this->actions[$action_name])){	
			return auth()->user()->can($this->actions[$action_name]);
		}
		return false;
    }

	//gets all the actions a user is allowed to doa nd returns it in a array.
	public function getActionsUserCanDo(Array $actions)
	{
		if($actions = $this->allActions){
			$output = array();
			foreach($actions as $action){
				if($this->canUserDoThisAction($action)){
					$output[] = $action;
				}
			}
			return $output;
		} else {
			return null;
		}
	}
	
	//gets all of the models actions and permisisons in a keypair array. returns null if empty
	public function getAllActionsAttribute()
	{
		$output = array();
		//check if model has default permissions. if so, lets add them.
		if($defaults = config('alpacajs.model-permissions.'. class_basename($this), false)){
			$output = $defaults;
		} 
		//check if model exist, if it does then check if it has actions.
		if($this->exists && $modelActions = $this->actions)
		{
			$output = array_merge($output, $modelActions);
		}
		
		return $output;
	}
	
	
	public function canUserDoTheseActions(Array $actions)
	{
		foreach($actions as $action)
		{
			if($this->canUserDoThisAction($action))
			{
				return true;
			}
		}
		abortJSON(400, "You are not allowed to do this action");
	}
	
	public function canUserDoThisAction(String $action)
	{
		if(!Auth::check()){ abortJSON(400, "You are not logged in"); }
		//check config for permisison and if user has it
		$defaults = config('alpacajs.model-permissions.'. class_basename($this), false);
		if($defaults &&  isset($defaults[$action])  && Auth::user()->hasPermissionTo($defaults[$action]))
		{
			return true;
		} 

		//check model for permisssion and if user has it
		if($this->exists && $permission = $this->getPermissionFromActionName($action))
		{
      if(auth()->user()->can($permission)){
        return true;
      }
		}
		//if the model has a child, check within the child model to see if it do the action
		if($this->exists && $child = config('alpacajs.model-data.' . class_basename($this) . '.child', false))
		{
			if($uuid = config('alpacajs.model-data.' . class_basename($this) . '.child_uuid', false)){ 
				$child = $child::findByUUID($this[$uuid]);
			}
				if($id = config('alpacajs.model-data.' . class_basename($this) . '.child_id', false)){ 
				$child = $child::find($this[$id]);
			}
			if(isset($child)){
				return auth()->user()->can($child->getPermissionFromActionName($action));
			}
		}
		
		return false;
	}
	
	//get the name of the permisison guarding a action. If it does not exist return false;
	public function getPermissionFromActionName($input)
	{
		if(($actions = $this->actions) != null){
			if(isset($actions[$input])){
				return $actions[$input];
			} 
		}
		return false;
	}
	
	
	//collect the models the user is allowed to do an action to
	public static function getAllModelsAllowedtoDoThis($action_name)
	{
		$models = self::all();
		$output = array();
		//there were no models so return nothing
		if($models == null)
		{
			return null;
		}
		//check if user has super permisison, then return all if he does
		if(self::staticCan($action_name))
		{
			return $models->pluck('name', 'uuid')->toArray();
		}
		foreach($models as $model)
		{
			if($model->canModel($action_name))
			{
				$output[$model->name] = $model->uuid;
			}
		}
		return $output;
	}
	
	
}
