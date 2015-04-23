<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BController extends BaseController
{
	public $model; //for loadModel function
	public $pageRobotsIndex = false;

	public $layout = "//layouts/main";

	public $tabs = array();
	public $filters = '';

	public $bodyClass = '';
	protected $_assetsUrl;     //путь к assets конкретной темы
	public $languageSelector = false;

	public function init(){

		parent::init();

		//меняем стандартный путь для темы
		app()->getThemeManager()->setBasePath(Yii::getPathOfAlias('core.themes'));
		app()->theme = 'webarch';
		
		//указываем обработчик ошибок
		app()->errorHandler->errorAction = "/core/admin/maintenance/backenderror";

		//определяем страницу входа, для фунцкии атентификации
		//пользователь будет перенаправлен на данную страницу, 
		//в случае если у него нет доступа к запрошенной странице
		user()->loginUrl = url('core/user/back/login');
		$this->isFront = false;

		/**
		 * REGISTER SCRIPTS
		 */

		//мета теги
		$this->registerMeta();
		
		$this->registerScripts();
		$this->registerStyles();
		$this->registerPlugins();
		
		cs()->registerPackage('bootstrap'); //register bootstrap script and styles
		cs()->registerPackage('backend-globals'); //стили и скрипты для темы
		cs()->registerPackage('project-specific-backend-globals'); //специфические для данного проекта глобальные скрипты и стили 
	
		cs()->registerCssFile(app()->getModule('core')->getModule('user')->assetsUrl.'/css/backend.css');
		cs()->registerScriptFile(app()->getModule('core')->getModule('user')->assetsUrl.'/js/backend.js');
	}

	public function filters()
	{
		return CMap::mergeArray(parent::filters(), [
			'AjaxCheckAccess', //важно чтобы этот фильтр был раньше
			['core.filters.UnlockFilter - unlock'],
			'postonly + addcomment, getcomments',
			'Rights'
		]);
	}

	/**
	 * Нужно для проверки входа для ajax действий
	 *
	 * если сессия истекла, то принесет окошко для повторного входа
	 */
	public function filterAjaxCheckAccess($filterChain){
		if(request()->isAjaxRequest && user()->isGuest && 
			!in_array($this->action->id, ['login', 'ajaxlogin', 'unlock'])) {
			
			Common::jsonSuccess(true, ['logout' => true]);
		}
		
		$filterChain->run();
	}

	public function behaviors(){
		return [
			//действия которые нужны для комментариев
			'core.widgets.comments.behaviors.CommentsActions'
		];
	}

	private function registerMeta(){
		$cs = Yii::app()->clientScript;

		$cs->registerMetaTag('width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no', 'viewport');
		$cs->registerMetaTag(lang(), 'language');
	}

	private function registerStyles(){
		$cs = Yii::app()->clientScript;

		//gridview это нужно для случаев, когда, рисуем gridview без CGridView
		$cs->registerCssFile(SGridView::assetsUrl()."/gridview.css"); //в конце можно убрать
		
		if(APPLICATION_ENV != 'devel') //google web fonts
			$cs->registerCssFile('http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,700,600');
	}

	private function registerScripts(){
		$cs = Yii::app()->clientScript;

		//BEGIN CORE JS FRAMEWORK
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
		$cs->registerCoreScript('cookie');
		
		//CORE SCRIPTS
		$cs->registerScriptFile($this->coreAssetsUrl .'/js/main/main-functions.js');
		$cs->registerScriptFile($this->coreAssetsUrl .'/js/main/small-plugins.js');
	}

	private function registerPlugins(){
		$cs = Yii::app()->clientScript;

		$cs->registerCssFile($this->rootAssetsUrl."/plugins/font-awesome/css/font-awesome.css");

		//handy events for your responsive design
		$cs->registerScriptFile($this->rootAssetsUrl .'/plugins/breakpoints/breakpoints.js'); //TODO: не знаю насколько это нам здесь нужно

		//jquery plugin for sidebar menus
		$cs->registerScriptFile($this->rootAssetsUrl .'/plugins/jquery-slider/jquery.sidr.min.js');
		$cs->registerCssFile($this->rootAssetsUrl .'/plugins/jquery-slider/css/jquery.sidr.light.css'); //важно чтобы он был до style.css

		//plugin to lazy load images
		$cs->registerScriptFile($this->rootAssetsUrl .'/plugins/jquery-unveil/jquery.unveil.min.js');

		$cs->registerPackage('jalerts'); //alerts
		$cs->registerPackage('selectstyler-backend'); //select-styler
		$cs->registerPackage("messenger"); //вывод сообщений

		$cs->registerScriptFile($this->rootAssetsUrl .'/plugins/jquery-slimscroll/jquery.slimscroll.min.js');
		
		//для history навигации
		$cs->registerCoreScript('bbq');
		$cs->registerCoreScript('history');
	}

	public function getAssetsUrl()
	{
		if($this->_assetsUrl===null)
		{
			$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
			$this->_assetsUrl = assets(Yii::getPathOfAlias('core.themes.webarch.assets'), false, -1, $recreate);
		}

		return $this->_assetsUrl;
	}

	public function actions(){
		return  CMap::mergeArray(parent::actions(), [
			'delete' => [
				'class' => 'modules.core.actions.DeleteAction'
			],
			'deleteselected' => [
				'class' => 'modules.core.actions.DeleteAction'
			],
			'status' => [
				'class' => 'modules.core.actions.StatusAction'
			]
		]);
	}

	// сохраняет выбранный статус из dropdown-а grid-view
	public function actionStatus($id){
		$m = request()->getParam('model', $this->model);

		$model = $this->loadModel($m, $id);

		$field = request()->getParam('fieldName', 'status');

		if(isset($_GET['val'])) 
			$model->$field = (int)$_GET['val'];
		else 
			$model->$field = (1 - $model->$field);

		$model->updateByPk($model->id, array($field=>$model->$field)); //чтобы не пересохранять значения из других таблиц
	}

	public function actionUserActivity(){
		
		if(!YII_DEBUG || APPLICATION_ENV == 'testproduction'){
			$command = Yii::app()->db2->createCommand();

			$user = $command->select('*')->
					from("user_activity")->
					where("id =:id", [':id'=>user()->id])->queryRow();

			$columns = [
					'id'=>user()->id, 
					'action'=>date('Y-m-d H:i:s'), 
					'url'=>request()->getParam('url')];

			if($user){
				$command->update('user_activity', $columns, 'id=:id', [':id'=>user()->id]);
			}
			else{
				$command->insert('user_activity', $columns);
			}
						
		}
	}

}