<?php

/**
* NotificationtemplateController - контроллер для работы с шаблонами писем и смс
*/
class TemplatesController extends BController
{

	public $model = 'NotificationTemplate'; //for loadModel function
	public $title = 'Шаблоны';
	public $languageSelector = 'grid';
	public $multilang = true;

	// /**
	// * Initializes the controller.
	// */
	public function init(){
		parent::init();

		$this->layout = $this->module->layoutTemplate;
	}

	public function actions(){
		return CMap::mergeArray(parent::actions(), [
			'create' => [
				'class' => 'modules.core.actions.CreateAction',
				'title' => 'Создание шаблона',
				'languageSelector'	=> 'tree',
				'viewAsArray'		=> false,
			],
			'update' => [
				'class' => 'modules.core.actions.UpdateAction',
				'title' => 'Редактирование шаблона',
				'languageSelector'	=> 'tree',
				'viewAsArray'		=> false,
				'multilang'			=> true,
			],
			'deleteselected' => [
				'class' => 'modules.core.actions.DeleteAction'
			]
		]);
	}

	public function actionIndex(){
		$this->redirect(['sms']);
	}

	public function actionSms(){
		$this->pageTitle = $this->title;
		$model = new NotificationTemplate('search');
		
		if($this->multilang){
			$lang = request()->getParam('_lang', param('defaultLanguage'));
			$model->localized($lang);
		}

		if(request()->isAjaxRequest){
			$post = request()->getParam('NotificationTemplate');
			$model->attributes = $post;
		}
		
		$this->render('index', ['model' => $model, 'type' => NotificationTemplate::SMS]);
	}

	public function actionEmail(){
		$this->pageTitle = $this->title;
		$model = new NotificationTemplate('search');
		
		if($this->multilang) {
			$lang = request()->getParam('_lang', param('defaultLanguage'));
			$model->localized($lang);
		}

		if(request()->isAjaxRequest){
			$post = request()->getParam('NotificationTemplate');
			$model->attributes = $post;
		}
		
		$this->render('index', ['model' => $model, 'type' => NotificationTemplate::EMAIL]);
	}
}