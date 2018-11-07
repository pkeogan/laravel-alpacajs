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
trait ApiTrait
{
	use ApiPermissionTrait;
	//checks, vaildates, and then creates and returns a model. False given if ever gone wrong.
	
	// get data from the models config via dot notation
	public function config($input)
	{
		if($this->exists)
		{
			//get the models config
			if(isset($this->attributes['config'])){
				return $this->config;
			} elseif($config = config('alpacajs.model-defaults.' . class_basename($this) . ".config", false)){
				return $config;
			} else {
				return null;
			}
		} else {
			return array_get(config('alpacajs.model-defaults.' . class_basename($this) . ".config"), $input);
		}
	}
	//gets the models uuid, if it doesnt exist it returns the config uuid for the model
	public function getUUID()
	{
		if($this->exists){
			return $this->uuid;
		} else {
			$config = array_flip(config("alpacajs.models"));
			if(isset($config[get_class($this)])){
				return $config[get_class($this)];
			}
		}
	}
	
	public function getBaseModelUUID()
	{
		$class_name = get_class($this);
		$config = array_flip(config("alpacajs.models"));
		foreach($config as $key=>$value)
		{
			if($class_name == $key)
			{
				return $value;
			}
		}
		
		dd('null');
	}
	
	public function deleteAPI()
	{
		if($this->exists)
		{
			$this->canUserDoThisAction('delete');
			$this->delete();
			return true;
		} else {
			abortJSON('404', "You can delete something that does not exist");
		}
	}
	
	public function setConfigApiAttribute($input)
	{
		if($input){
			$this->config = array_convert_to_keypair($input, 'name', 'value');
		} else {
			$this->config = null;
		}
	}
	
	public function getConfigApiAttribute()
	{
		if($this->config){
			return array_convert_to_single_dimension($this->config, 'name', 'value');
		} else {
			return null;
		}
	}
	
	public function getActionsApiAttribute()
	{
		if($this->actions){
			return array_convert_to_single_dimension($this->actions, 'action_name', 'permission_name');
		} else {
			return null;
		}
	}
	
	public function setActionsApiAttribute($input)
	{
		if($input){
			$this->actions = array_convert_to_keypair($input, 'action_name', 'permission_name');
		} else {
			$this->actions = null;
		}
	}
	
  //get the models partent's uuid
	public function getParentModelUuidAttribute()
	{
      $child_uuid = config('alpacajs.model-data.' . class_basename($this) . ".child_uuid", false);
      $child_id = config('alpacajs.model-data.' . class_basename($this) . ".child_id", false);
      $child = config('alpacajs.model-data.' . class_basename($this) . ".child", false);

			
		if($child_uuid && $child){
			$child = $child::findByUUID($this->attributes[$child_uuid]);
		}

    if($child_id && $child){
			$child = $child::find($this->attributes[$child_id]);	
		}
    

    if($child != null){
      return $child->uuid;
    }

		return null;
	}
	
	
	public function checkValidateAndCreate(Request $request)
	{
		$this->canUserDoThisAction('create');
			
		$validator = validator($request->all(), $this->rulesAPI());
		if (!$validator->errors()->isEmpty()) {
			abortJSON(400, $validator->errors());
		}
		$attributes = $validator->valid();

		if(method_exists($this, 'createAPI'))
		{
			$this->createAPI($attributes);
		} else {
			$this::create($attributes);
		}
		abortJSON(200, 'Request has been processed with no errors.');
	}
	
	public function checkValidateAndUpdate(Request $request)
	{
		$this->canUserDoThisAction('edit');
		$validator = validator($request->all(), $this->editRulesAPI());
		if (!$validator->errors()->isEmpty()) {
			abortJSON(400, $validator->errors());
		}
		$attributes = $validator->valid();
		
		if(method_exists($this, 'updateAPI'))
		{
			$this->updateAPI($attributes);
		} else {
			$this->update($attributes);
		}
		
		abortJSON(200, 'Request has been processed with no errors.');
	}
	
	public function checkAndRead(Request $request, $query)
	{
		$this->canUserDoThisAction('view');
		if($query && is_array($query))
		{
			return response()->json($this->readWithQuery($query));
		} else {
			return response()->json($this);
		}
	}
	
	public function readWithQuery($query)
	{
		foreach($query as $key=>$value)
		{
			$method = "apiAction" . $key;
			if(method_exists($this, $method))
			{
				return $this->$method($value);
			}
		}
	}
	
	public function getCustomActionsAttribute(){
		return config('alpacajs.model-custom-actions.' . class_basename($this), false);
	}
	
	public function getActionButtons()
	{
		if($customActions = $this->customActions){
			$output = array();
			foreach($customActions as $action=>$data){
					if($this->canUserDoThisAction($action)){
						if(!isset($json[$action])){
							$output[$action] = array_except($data, ['schema', 'options', 'postRender']);	
						}
					}
			}
			return $output;
		} else {
			return null;
		}
	}
	
	
	public function checkAndDelete(Request $request)
	{
		dd('deleting this model');
		return false;
	}
	
	public function getButtons($query = false)
	{
		$json = array();
		
		if($query && isset($query['direct']) && $query['direct'])
		{
			// if the model your trying to get actions for exists then lets get those permissions as well
			if($customActions = config('alpacajs.model-custom-actions.' . class_basename($this), false))
			{
				foreach($customActions as $action=>$data)
				{
					if($this->canUserDoThisActionDirectly($action)){
						if(!isset($json[$action])){
							$json['custom_actions'][$action] = array_except($data, ['schema', 'options', 'postRender']);	
						}
					}
				}
			}	
		} else {
		
			if($this->canUserDoThisAction('delete')){
				$json[] = "delete";
			}
			if($this->canUserDoThisAction('perm-delete')){
				$json[] = "perm-delete";
			}
			if($this->canUserDoThisAction('restore')){
				$json[] = "restore";
			}
			if($this->canUserDoThisAction('clone')){
				$json[] = "clone";
			}
			if($this->canUserDoThisAction('edit')){
				$json[] = "edit";
			}
			if($this->canUserDoThisAction('create')){
				$json[] = "create";
			}
			if($this->canUserDoThisAction('view')){
				$json[] = "view";
			}

			//check for custom actions, if we have any of them lets load them up to the user
			// if the model your trying to get actions for exists then lets get those permissions as well
			if($customActions = config('alpacajs.model-custom-actions.' . class_basename($this), false))
			{
				foreach($customActions as $action=>$data)
				{
					if($this->canUserDoThisAction($action)){
						if(!isset($json[$action])){
							$json['custom_actions'][$action] = array_except($data, ['schema', 'options', 'postRender']);	
						}
					}
				}
			}	
			
		}
		
		
		return response()->json($json);


	}
	

	
	public function lang($input)
	{
		return __('models.' . class_basename($this) . '.' .$input);
	}
	
	public function getButtonTemplate($onclick, $title, $url, $tooltip, $label)
	{
		return '<button type="button" onclick="'.$onclick.'" class="btn btn-default" 
							data-alpaca-title="'. $title . '" 	
							data-alpaca-url="'. $url . ' 
							data-toggle="tooltip" title="'.$tooltip.'">'.$label.'</button>';
	}
	
	//get the models schema data and its config settings schema
	public function getSchema($config = [])
	{
		$default = array();	
		$schema = array();
		//does the model exist?
		if($this->exists)
		{
			//
			if($parent_class = config('alpacajs.model-data.' . class_basename($this) . '.parent', [])){
				if($schema_temp = config('alpacajs.model-defaults.' . class_basename($parent_class) . '.schema', [])){ 
					$default = $schema_temp;
				}
			}
		} else {
			if($schema_temp = config('alpacajs.model-defaults.' . class_basename($this) . '.schema', [])){ 
				$default = $schema_temp;
			}
		}

		
		if($this->exists && isset($this->data['schema']) && $this->data['schema'] != null){ 
			$schema = json_decode($this->data['schema'], true);
		}
		return array_merge($schema, $default);
	}
	
	//get the models parent schema
	public function getClassSchema()
	{
		$default = array();	
		if($this->exists)
		{
			//check if this model has a child model, if so we need to grab the all the schema from that aswell so the data will be displated
			if($child = config('alpacajs.model-data.' . class_basename($this) . '.child', [])){ 
				if($uuid = config('alpacajs.model-data.' . class_basename($this) . '.child_uuid', [])){ 
					$child = $child::findByUUID($uuid);
				}
					if($id = config('alpacajs.model-data.' . class_basename($this) . '.child_id', [])){ 
					$child = $child::find($this[$id]);
				}
				return $child->getSchema();
			}
			
			//check if this model has defined schema
			if($schema_temp = config('alpacajs.model-defaults.' . class_basename($this) . '.schema', [])){ 
				return $schema_temp;
			}
			
			return $default;
		} else {
			dd('Parent Model Given, we need to edit a model that actually exists');
		}
		
	}
	
	//get the models options data
	public function getOptions()
	{
		$default = array();	
		$options = array();
		if($this->exists && $parent_class = config('alpacajs.model-data.' . class_basename($this) . '.parent', []))
		{
			if($options_temp = config('alpacajs.model-defaults.' . class_basename($parent_class) . '.options', [])){ 
				$default = $options_temp;
			}
		} else {
			if($options_temp = config('alpacajs.model-defaults.' . class_basename($this) . '.options', [])){ 
				$default = $options_temp;
			}
		}
		
		if($this->exists && isset($this->data['options']) && $this->data['options'] != null){ 
			$options = json_decode($this->data['options'], true);
		}
		
		return  array_merge($default, $options);
	}
	
	//get the models parent schema
	public function getClassOptions()
	{
		$default = array();	
		if($this->exists)
		{
			//check if this model has a child model, if so we need to grab the all the schema from that aswell so the data will be displated
			if($child = config('alpacajs.model-data.' . class_basename($this) . '.child', false)){ 
				if($uuid = config('alpacajs.model-data.' . class_basename($this) . '.child_uuid', false)){ 
					$child = $child::findByUUID($uuid);
				}
					if($id = config('alpacajs.model-data.' . class_basename($this) . '.child_id', false)){ 
					$child = $child::find($this[$id]);
				}
				return $child->getOptions();
			}
			
			//check if this model has defined options
			if($options_temp = config('alpacajs.model-defaults.' . class_basename($this) . '.options', false)){ 
				return $options_temp;
			}
			
			return $default;
		} else {
			dd('Parent Model Given, we need to edit a model that actually exists');
		}
		
	}
	
	//get the models postrender data
	public function getPostRender()
	{
		$default = '';	
		$postRender = '';
		if($this->exists && $parent_class = config('alpacajs.model-data.' . class_basename($this) . '.parent', false))
		{
			if($postRender_temp = config('alpacajs.model-defaults.' . class_basename($parent_class) . '.postRender', false)){ 
				$postRender = str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', $postRender_temp);
			}
		} else {
			if($postRender_temp = config('alpacajs.model-defaults.' . class_basename($this) . '.postRender', false)){ 
				$postRender = str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', $postRender_temp);
			}
		}
		
		if($this->exists || isset($this->data['postRender'])){ 
			$postRender .= str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', $this->data['postRender']);
		}
		
		return $postRender;
	}
	
		//get the models parent schema
	public function getClassPostRender()
	{

		
		$default = '';	
		if($this->exists)
		{
			//check if this model has a child model, if so we need to grab the all the schema from that aswell so the data will be displated
			if($child = config('alpacajs.model-data.' . class_basename($this) . '.child', false)){ 
				if($uuid = config('alpacajs.model-data.' . class_basename($this) . '.child_uuid', false)){ 
					$child = $child::findByUUID($uuid);
				}
					if($id = config('alpacajs.model-data.' . class_basename($this) . '.child_id', false)){ 
					$child = $child::find($this[$id]);
				}
				return str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', $child->getPostRender());

			}
			
			//check if this model has defined options
			if($postRender_temp = config('alpacajs.model-defaults.' . class_basename($this) . '.postRender', false)){ 
					return $postRender_temp = str_replace(array("\r\n", "\r", "\n","\\", "\t"), '', $postRender_temp);
			}
			
		} else {
			dd('Parent Model Given, we need to edit a model that actually exists');
		}
		
	}
	
}