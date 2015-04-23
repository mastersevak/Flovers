<?php


class VideoController extends BController
{
	
	public $model = 'Video'; //for loadModel function
	public $title = 'Видео материалы';

	public function filters(){
		return CMap::mergeArray(parent::filters(), array(
			'postOnly + ajaxUpdate'
		));
	}

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				'title' => 'Видео материалы'
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				'title' => 'Создание видео',
				'viewAsArray' => false,
				'beforeRender' => function(){
					$this->layout = '//layouts/tabs';
					$this->tabs = array(
						'main'=>t('admin', 'Общая информация'),
						'meta'=>t('admin', 'Мета данные'),
					);

					cs()->registerScriptFile($this->module->assetsUrl.DS."js".DS."video.js");
				}
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				'title' => 'Редактирование видео',
				'viewAsArray' => false,
				'beforeRender' => function(){
					$this->layout = '//layouts/tabs';
					$this->tabs = array(
						'main'=>t('admin', 'Общая информация'),
						'meta'=>t('admin', 'Мета данные'),
					);

					cs()->registerScriptFile($this->module->assetsUrl.DS."js".DS."video.js");
				}
			]
		]);
	}
}
