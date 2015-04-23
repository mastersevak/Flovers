<?php 


/**
 * StatusAction
 * 
 * Changes status for model
 */
 class StatusAction extends CAction
 {
 	public $model;

 	public function run($id){

        $model = $this->controller->loadModel(request()->getParam('model', $this->controller->model), $id);

        $field = request()->getParam('fieldName', 'status');

        $model->$field = isset($_GET['val']) ? (int)$_GET['val'] : (1 - $model->$field);

        if($model->edit([$field => $model->$field]))
			Common::jsonSuccess(true);

		Common::jsonError('не удалось изменить статус');
 	}

 }