<?php 


/**
 * IndexAction
 * 
 * Creates a new model
 */
 class IndexAction extends CAction
 {
 	public $title = 'Список';
 	public $breadcrumbs;
 	public $beforeRender;
 	public $view = 'index';
 	public $ajaxView;
 	public $model;

 	public $multilang = false;
 	public $languageSelector;

 	// метод который показывает список ....
	public function run()
	{
		$lang = request()->getParam('_lang', param('defaultLanguage'));

		if(!$this->model){
			$model = $this->controller->model;
			$model = new $model('search');
			if($this->multilang) $model->localized($lang);
		}
		elseif(is_string($this->model)){
			$model = $this->model;
			$model = new $model('search');
			if($this->multilang) $model->localized($lang);
		}
		else{
			$model = $this->model;
			if($this->multilang) $model->localized($lang);
		}

        $params = [];

		if($this->beforeRender){
			call_user_func_array($this->beforeRender, [$model, &$params]);
		}

		$params['model'] = $model;

		if(Yii::app()->request->isAjaxRequest && Yii::app()->request->getParam('ajax')){
        	$this->controller->renderPartial($this->ajaxView ? $this->ajaxView : $this->view, $params);
		}
		else{
			if($this->languageSelector)
				$this->controller->languageSelector = $this->languageSelector;
			$this->controller->pageTitle = $this->title;
			$this->controller->breadcrumbs = $this->breadcrumbs ? $this->breadcrumbs : [$this->controller->title];
			$this->controller->render($this->view, $params);
		}		
	}
 }