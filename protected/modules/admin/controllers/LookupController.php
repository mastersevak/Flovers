<?php


class LookupController extends BController {
	
	public $model = 'Lookup'; //for loadModel function
	public $title = 'Справочник';

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Справочник',
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
	
	
	
}