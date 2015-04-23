<?php


class BackController extends BController
{
	
	public $model = 'Page'; //for loadModel function
	public $title = 'Страницы';

	public function filters(){
		return CMap::mergeArray(parent::filters(), array(
			'postOnly + ajaxUpdate'
		));
	}

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Статические страницы',
				'languageSelector' => 'grid',
				'multilang' => true,
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				'title' => 'Создание страницы',
				'viewAsArray' => false,
				'languageSelector' => 'tree',
				'slugger' => true,
				'beforeRender' => function(){
					$this->layout = '//layouts/tabs';
					$this->tabs = array(
						'main'=>t('admin', 'Общая информация'),
						'meta'=>t('admin', 'Мета данные'),
					);
				}
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				'title' => 'Редактирование страницы',
				'viewAsArray' => false,
				'languageSelector' => 'tree',
				'multilang' => true,
				'slugger' => true,
				'beforeRender' => function(){
					$this->layout = '//layouts/tabs';
					$this->tabs = array(
						'main'=>t('admin', 'Общая информация'),
						'meta'=>t('admin', 'Мета данные'),
					);
				}
			]
		]);
	}
}
