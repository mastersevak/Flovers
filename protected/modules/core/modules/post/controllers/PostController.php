<?php


class PostController extends BController
{
	public $model = 'Post'; //for loadModel function
	public $title = 'Пост';

	public $indexView;
	public $formView;

	public function init(){
		parent::init();

		$this->indexView  = $this->indexView  ? $this->indexView  : "core.modules.post.views.post.index";
		$this->createView = $this->createView ? $this->createView : "core.modules.post.views.post.form";
		$this->updateView = $this->updateView ? $this->updateView : "core.modules.post.views.post.form";
	}

	public function filters(){
		return CMap::mergeArray(parent::filters(), array(
			'postOnly + ajaxUpdate'
		));
	}

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'index' => [
				'class' => 'modules.core.actions.IndexAction',
				// 'title' => 'post',
				'view'	=> $this->indexView,
			],
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				// 'title' => 'Создание поста',
				'view'	=> $this->createView,
				'viewAsArray' => false,
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
				// 'title' => 'Редактирование поста',
				'view'	=> $this->updateView,
				'viewAsArray' => false,
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
