<?php 


/**
 * CreateAction
 * 
 * Creates a new model
 */
 class CreateAction extends CAction
 {
	public $title = 'Создание записи';
	public $afterSave, $beforeSave, $beforeRender;
	public $breadcrumbs;
	public $model;
	public $view;
	public $slugger;

	public $viewAsArray = true;
	public $languageSelector;

	public function run(){

		$modelName = $this->model && is_string($this->model) ? $this->model : $this->controller->model;
		$model = new $modelName('create');

		if($this->slugger){
			$model->attachBehavior('slugger', [
				'class' => 'core.behaviors.SlugBehavior',
				'sourceAttribute' => isset($this->slugger['sourceAttribute']) ? $this->slugger['sourceAttribute'] : 'title',
				'slugAttribute' =>  isset($this->slugger['slugAttribute']) ? $this->slugger['slugAttribute'] : 'slug',
			]);
		}

		// Uncomment the following line if AJAX validation is needed
		$this->controller->performAjaxValidation($model);

		$this->controller->pageTitle = $this->title;
		// $this->controller->pageDesc  = t('admin', 'Заполните поля для создания записи');

		$this->controller->breadcrumbs = $this->breadcrumbs ? $this->breadcrumbs : [$this->controller->title => ['index'], $this->controller->pageTitle];

		
		if(isset($_POST[$modelName]))
		{
			$model->attributes = $_POST[$modelName];

			if($this->beforeSave){
				call_user_func_array($this->beforeSave, [$model]);
			}

			if($model->validate()){
				if(!empty($model->withRelatedObjects)){
					$result = $model->withRelated->save(false, $model->withRelatedObjects);
				}
				else
					$result = $model->save(false);
			
				if($result){

					if($this->afterSave){
						call_user_func_array($this->afterSave, [$model]);
					}

					if(request()->isAjaxRequest)
						Common::jsonSuccess(true);

					//объязательно
					if(request()->getParam('close') == 'true'){
						$this->controller->redirect(user()->gridIndex);
					}
					else 
						$this->controller->redirect(['update', 'id'=>$model->id]);
				}
			}			
		}

		if($this->viewAsArray){ // загружаем форму через конструктор форм

			$config = require($this->controller->module->basePath. '/views/' . $this->controller->id . '/form.php');

			if(!isset($config['buttons']))
				$config['buttons'] = ['group' => ($model->isNewRecord ? 'create' : 'update')];

			$form = new SForm($config, $model);

			$params = compact('form', 'model');
			$view = 'update';
		}
		else{
			$params = compact('model');
			$view = $this->view ? $this->view : 'form';
		}
		
		if($this->beforeRender){
			call_user_func_array($this->beforeRender, [$model, &$params]);
		}

		if(!request()->isAjaxRequest)
			if($this->languageSelector)
				$this->controller->languageSelector = $this->languageSelector;
			$this->controller->render($view, $params);
	}
}