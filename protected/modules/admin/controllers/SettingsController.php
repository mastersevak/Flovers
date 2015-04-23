<?php

/**
 * Store Module SettingsController
 *
 * Контроллер для управления настройками
 */
class SettingsController extends BController {
	
	public $model = 'Settings';
	public $title = 'Настройки'; 

	public function filters(){
		return CMap::mergeArray(parent::filters(), array(
			'postOnly + ajaxUpdate'
		));
	}

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Настройки',
				'beforeRender' => function($model){
					$model->category = 'unknown'; //для того чтобы вначале таблица была пустая

					Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/settings.js');

					if(Yii::app()->request->getParam('category')){
						$model->category = Yii::app()->request->getParam('category');
					}
				}
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				//'viewAsArray' => false
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				//'viewAsArray' => false
			]
		]);
	}

	public function actionAjaxUpdate($id){
		$model = $this->loadModel($this->model, $id);

		$model->value = request()->getParam('value');
		$model->save();
	}
	
}

