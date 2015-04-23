<?php 


/**
 * DeleteAction
 * 
 * Deletes a new model
 */
 class DeleteAction extends CAction
 {
 	public $model;

 	public function run($id = false, $model = false, $forceDelete = false){

 		$modelName = $this->model && is_string($this->model) ? $this->model : 
 						(request()->getParam('model') ? request()->getParam('model') : $this->controller->model);

 		if($id) {
 			//delete one model
 			$result = $this->controller->loadModel($modelName, $id)->delete();
 			if(!request()->isAjaxRequest && $result)
 				$this->controller->redirect(user()->gridIndex);

 			Common::jsonSuccess(true);
 		}
 		else{
 			$items = Common::getChecked('items');
 			
 			if($items) {
 				if(!$forceDelete)
			        foreach ($items as $id)
			            $this->controller->loadModel($modelName, $id)->delete();
			    else{
			    	$criteria = new SDbCriteria;
			    	$criteria->compare('id', $items);
			    	CActiveRecord::model($modelName)->deleteAll($criteria);
			    }

			    Common::jsonSuccess(true);
 			}
 		}

 		Common::jsonError("Ошибка");
 	}

 	
 }