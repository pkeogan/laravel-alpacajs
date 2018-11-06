<?php

namespace Pkeogan\LaravelAlpacaJS\Classes;

use Webpatser\Uuid\Uuid as PackageUuid;
use Pkeogan\LaravelAlpacaJS\Traits\ReadTrait;


class Api
{
	
	use ReadTrait;
	
	protected $uuid;
	protected $id;
	protected $class;
	protected $path;
	protected $model;
	
	function __construct() {
		return $this;
	}
	
	public function getModelFromUUIDOrSlug($input, $parent)
	{
		$temp = false;
		if($this->is_uuid1($input))
		{
			$temp = $this->getModelFromUUIDAndWithParent($input, $parent);
		} else {
			$temp = $this->getModelFromSlugWithParent($input, $parent);
		}
		
		if($temp){
			return $temp;
		} else {
		    return abort(401, "Model or Slug Not Found");
		}
	}
	
	public function getModelFromUUIDOrClass($input)
	{
		if($this->is_uuid1($input))
		{
			return $this->searchModelsForUUID($input);
		} else {
			return $this->getModelFromClassName($input);
		}
	}
	
	public function getModelFromClassName(String $input)
	{
		$config = config('alpacajs.models');
		foreach($config as $key=>$value)
		{
			if(strtolower(class_basename($value)) == strtolower($input))
			{
				return app()->make($value);
			}
		}
		
		return abort(401, "Model or UUID Not Found");
	}
	
	//Gets a model from a UUID. IF invalid, will abort and give error.
	public function getModelFromUUID(String $uuid)
	{
		if($this->is_uuid1($uuid))
		{
			return $this->searchModelsForUUID($uuid);
		} else {
			abort(401, "Given UUID Is not a valid UUID1 Format");
		}
	}
	
		
		//Gets a model from a UUID. IF invalid, will abort and give error.
		public function getModelFromSlugWithParent(String $slug, $parent)
		{
		if(!$parent){
			abort(401, "Child model needs a parent to resolve, no parent given");
		}
		return $parent::where('slug', $slug)->first();
	}
	
	//Gets a model from a UUID. IF invalid, will abort and give error.
	public function getModelFromUUIDAndWithParent(String $uuid, $parent)
	{
		if(!$parent){
			abort(401, "Child model needs a parent to resolve, no parent given");
		}
		if($this->is_uuid1($uuid))
		{
			return $this->searchModelsForUUIDWithinParent($uuid, $parent);
		} else {
			abort(401, "Given UUID Is not a valid UUID4 Format");
		}
	}
	
	//Find a model underneath a given parent. if none found return false;
	public function searchModelsForUUIDWithinParent(String $uuid, $parent)
	{
		if($parent->exists)
		{
			return $parent->children::findByUuid($uuid);
		} else {
			return $parent::findByUuid($uuid);
		}
	}
	
	//Find a mdoel by a given UUID that has been verifed. returns model if found, returns false if not found
	public function searchModelsForUUID(String $uuid)
	{
		if(!$modelPaths = config('alpacajs.models', false)){
			abort(500, "Error with config file");
		}
			
		if($model = $this->searchConfigForUUID($uuid)){
			return $model;
		}
	
		foreach($modelPaths as $key=>$modelPath)
		{			
			if($this->has_findByUUID_function($modelPath, $uuid)){
				if($model = $this->searchClassForUUID($modelPath, $uuid)){
					return $model;
				}
			}
		}
		
		return false;
	}
	
	//check config to see if the uuid matches a model. if so return an empty copy of it
	public function searchConfigForUUID(String $uuid)
	{
		if($modelPath = config("alpacajs.models.".$uuid, false)){
			return new $modelPath;
		} else {
			return false;
		}
	}
	
	//search a class by a uuid, if found return it, if not return false;
	public function searchClassForUUID(String $modelPath, String $uuid)
	{
		return $modelPath::findByUuid($uuid);
	}
	
	//check if a class has the ability to find by uuids. if it does return true if not return false
	public function has_findByUUID_function(String $modelPath, String $uuid)
	{
		return (method_exists($modelPath, "findByUuid")) ? true : false;
	}
	
	//cerate a uuid1 and return it
	public static function generateUUID()
    {
        return PackageUuid::generate()->string;
    }
	
	
	//Check if string is valid UUID4
	public function is_uuid4(String $input)
    {
		return (preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $input)) ? true : false;
	}
	
	//Check if string is valid UUID1
	public function is_uuid1(String $input)
    {
		return (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $input)) ? true : false;
	}
}