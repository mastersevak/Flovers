<?php

class LookupController extends BController {

	public $model = 'Lookup'; //for loadModel function
	public $title = 'Справочник';

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Справочник',
				'languageSelector' => 'grid',
				'multilang' => true,
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				'languageSelector' => 'tree',
				'viewAsArray' => false 
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				'languageSelector' => 'tree',
				'multilang' => true,
				'viewAsArray' => false
			]
		]);
	}
}