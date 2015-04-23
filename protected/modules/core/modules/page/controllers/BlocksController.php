<?php


class BlocksController extends BController
{

	public $model = 'Block'; //for loadModel function
	public $title = 'Блоки';

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Текстовые блоки',
				'languageSelector' => 'grid',
				'multilang'		=> true,
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				'title' => 'Создание блока',
				'viewAsArray' => false,
				'languageSelector' => 'tree',
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				'title' => 'Редактирование блока',
				'viewAsArray'	=> false,
				'languageSelector' => 'tree',
				'multilang'		=> true,
			]
		]);
	}
}
