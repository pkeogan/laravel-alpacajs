<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

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
			} elseif ($this->action == "edit") {
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
			$clone = $this->model->create($model->toArray());
			if ($clone->save()) {
					$cloned++;
				} else {
					$kept++;
				}
		}
		if($kept == 0)
		{			
			abortJSON(200, $cloned . ' out of ' . $cloned . ' requested clones were made.');

		} else {
			abortJSON(500, 'There was an error cloneing the selected resource(s): ' . $kept . ' out of ' . $kept + $cloned . ' were cloned.');
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
		if(!$this->model::staticCan($action_name))
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

    private function respondWithData()
    {
		$this->checkPermission('edit');
		if(!$this->model){abortJSON(404, 'The resource you are looking for cannot be found.');}
        return response()->json($this->model->dataAPI());
    }

    //retracted and not in use
    private function respondWithSchema()
    {
        return response()->view($this->schema, ['options' => true], 200)->header('Content-Type', 'application/json');

        $json = new HtmlString($this->view->make($this->schema, ['schema' => true])->render());
        return response()->json($json->toHtml());
    }

    private function respondWithOptions()
    {
        return response()->view($this->options, ['options' => true], 200)->header('Content-Type', 'application/json');
    }

    private function respondWithView()
    {
        return response()->json($this->viewSource);
    }

}
