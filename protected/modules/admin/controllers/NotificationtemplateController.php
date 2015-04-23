<?php

/**
* NotificationtemplateController - контроллер для работы с шаблонами писем и смс
*/
class NotificationtemplateController extends BController
{
	
	public $model = 'NotificationTemplate'; //for loadModel function
	public $title = 'Шаблоны';

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Шаблоны',
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				'title' => 'Создание шаблона',
				'viewAsArray' => false
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				'title' => 'Редактирование шаблона',
				'viewAsArray' => false
			],
			'deleteselected' => [
				'class' => 'modules.core.actions.DeleteAction'
			],

		]);
	}

}
