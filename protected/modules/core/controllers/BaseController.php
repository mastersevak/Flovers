<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends CController
{

	public $layout = "//layouts/main";
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = [];
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = [];

	public $iurls; //initialize in init()

	/* SEO Vars */
	public $pageDesc = '';

	public $isFront;

	protected $_assetsUrl;     //путь к assets конкретной темы
	protected $_rootAssetsUrl; //путь к assets общей для всех тем
	protected $_coreAssetsUrl; //путь к assets для модуля core

	private $_behaviorIDs = [];

	private static $_subdomain;
	private static $_host;

	public function filters()
    {
        return CMap::mergeArray(parent::filters(), [
            'accessControl'
        ]);
    }

    public function accessRules()
    {
    	$rules = [];

    	$blockedIps = Settings::item('blockedIps');

    	if($blockedIps){

    		if(is_string($blockedIps)){
    			$blockedIps = explode(',', $blockedIps);
    			foreach($blockedIps as &$ip){
    				$ip = trim($ip);
    			}
    		}

    		if(is_array($blockedIps))
	    		$rules = [
		            ['deny', 'ips'=>$blockedIps, 'deniedCallback' => function (){
				    	echo "У вас нет доступа. Ваш ip заблокирован";
				    }],

		        ];
    	}


        return $rules;
    }

	public function init(){

		//init
		parent::init();

		/**
		 * вызываем раньше всех, а было бы супер если бы при каждом
		 * использовании js, сами вызывали registerCoreScript('jquery'),
		 * либо через зависимость в пакетах
		 */
		Yii::app()->clientScript->registerCoreScript('jquery', CClientScript::POS_END);

		if (request()->isAjaxRequest) {
			$this->layout = '//layouts/clear';
		}
		
		Yii::import('core.extensions.JsTrans.JsTrans');
        new JsTrans(['admin','user','property','front'], ['ru','en']);
	}

	public function actions(){
        $model = $this->model;

        /**
         * Не самый красивый способ
         */
        return CMap::mergeArray(parent::actions(), [
            'sorting' => [
                'class' => 'core.behaviors.sortable.SortableAction',
                'model' => $model ? $model::model() : null,
            ],
            'ajaxslug'   => ['class' => 'core.actions.AjaxSlugAction']
        ]);
    }

    public function getCoreAssetsUrl(){
        if($this->_coreAssetsUrl===null)
        {
        	$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
            $this->_coreAssetsUrl = assets(Yii::getPathOfAlias('core.assets'), false, -1, $recreate);
        }

        return $this->_coreAssetsUrl;
    }

    public function getRootAssetsUrl(){
        if($this->_rootAssetsUrl===null)
        {
        	$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
            $this->_rootAssetsUrl = assets(Yii::getPathOfAlias('application.assets'), false, -1, $recreate);
        }

        return $this->_rootAssetsUrl;
    }

    public function getAssetsUrl()
    {
        if($this->_assetsUrl===null)
        {
        	$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
            $this->_assetsUrl = assets(Yii::getPathOfAlias('webroot.themes.'.app()->theme->name.'.assets'), false, -1, $recreate);
        }

        return $this->_assetsUrl;
    }

	public function checkAccess($min_level) {

		if (user()->isGuest || user()->getState('role') > $min_level ) {
			throw new CHttpException(403, 'You have no permission to view this content');
		}
	}

	/**
	 * Возвращает кешированную модель, по ее первичному ключу
	 *
	 * @param  mixed  	$model     	модель, или имя модели
	 * @param  mixed 	$id 		либо id, либо array('condition' => 'condition string', 'params' => []) условие
	 * @param  array  	$with      [description]
	 *
	 *
	 * @return CActiveRecord
	 */
	public function loadModel($model, $id = false, $with = false, $multilang = false)
	{

		$result = false;

		if(!$id) {
			$model = new $model('create');

			if($with)$model->loadWithRelations = $with;

			return $model;
		}

		if(is_string($model))
			$model = CActiveRecord::model($model);

		if($multilang) $model = $model->multilang();

		if($with) $model->with($with);

		if(is_numeric($id)){
			$result = $model->findByPk($id);
		}
		else{
			if(isset($id['condition']))
				$result = $model->find($id['condition'], $id['params'] ? $id['params'] : []);
		}

		if(!$result)
			throw new CHttpException(404, 'The requested page does not exist.');

		if($with) $result->loadWithRelations = $with;

		return $result;
	}

	public function performAjaxValidation($model){

		if(request()->isAjaxRequest && isset($_POST['ajax'])){
			$result = [];
			foreach(func_get_args() as $argument){

				if($argument->loadWithRelations){
					$relations = $argument->relations();

					$loadWithRelations = [];
					foreach($argument->loadWithRelations as $one){
						if(isset($relations[$one]) && $relations[$one][0] == AR::HAS_ONE)
							$loadWithRelations[] = $one;
					}

					foreach($loadWithRelations as $relation){
						$result = CMap::mergeArray($result, CJSON::decode(CActiveForm::validate($argument->$relation)));
					}
				}


				$result =  CMap::mergeArray($result, CJSON::decode(CActiveForm::validate($argument)));
			}

			echo CJSON::encode($result);
			Yii::app()->end();
		}
	}

	//для вывода CSRF, для последующего доступа из jquery
	protected function afterRender($view, &$output){

		$tokenName = Yii::app()->request->csrfTokenName;
		$tokenValue = Yii::app()->request->getCsrfToken();

		$output .= 	CHtml::openTag('div', ['style'=>'display:none']).
					CHtml::hiddenField('csrf_name', $tokenName).
					CHtml::hiddenField('csrf_value', $tokenValue).
					CHtml::hiddenField('nodejs_url', param('nodejsUrl')).
					CHtml::hiddenField('nodejs_user', user()->id).
					CHtml::closeTag('div');
	}

	/**
	 * Hook который позволяет создавать действия внутри behavior
	 * предназначенный для контроллера
	 */
    public function createAction($actionID)
    {
        $action = parent::createAction($actionID);
        if($action !== null) return $action;

        foreach($this->behaviors() as $behaviorID => $behavior)
        	$this->_behaviorIDs[] = $behaviorID;

        foreach($this->_behaviorIDs as $behaviorID)
        {
                $object = $this->asa($behaviorID);
                if($object->getEnabled() && method_exists($object,'action'.$actionID))
                        return new CInlineAction($object,$actionID);
        }
    }

    /**
	 * это нужно для корректной работы createAction
	 */
	public function attachBehavior($name, $behavior)
    {
        $this->_behaviorIDs[] = $name;
        parent::attachBehavior($name, $behavior);
    }

	/**
	 * Set flash messages
	 * @param string $message
	 */
	public function setFlashMessage($message)
	{
		$currentMessages = Yii::app()->user->getFlash('messages');

		if (!is_array($currentMessages))
			$currentMessages = [];

		Yii::app()->user->setFlash('messages', CMap::mergeArray($currentMessages, [$message]));
	}

	/**
	 * Add flash message
	 * @param $message
	 */
	public  function addFlashMessage($message)
	{
		$currentMessages = Yii::app()->user->getFlash('messages');

		if (!is_array($currentMessages))
			$currentMessages = [];

		Yii::app()->user->setFlash('messages', CMap::mergeArray($currentMessages, [$message]));
	}

	public function filterRights($filterChain)
	{
		$filter = new RightsFilter;
		$filter->allowedActions = $this->allowedActions();
		$filter->filter($filterChain);
	}


	/**
	* @return string the actions that are always allowed separated by commas.
	*/
	public function allowedActions()
	{
		return '';
	}

	/**
	* Denies the access of the user.
	* @param string $message the message to display to the user.
	* This method may be invoked when access check fails.
	* @throws CHttpException when called unless login is required.
	*/
	public function accessDenied($message=null)
	{
		if( $message===null )
			$message = Rights::t('core', 'You are not authorized to perform this action.');

		$user = Yii::app()->getUser();
		if( $user->isGuest===true )
			$user->loginRequired();
		else
			throw new CHttpException(403, $message);
	}

	//очищаем кеш
	public function actionClearCache(){
        Yii::app()->cache->flush();
        echo "Кеш очищен";
       
        $file = file_put_contents(app()->runtimePath . DS . "lastModified.log", date("Y-m-d H:i:s"));
    }

    //очищаем assets
	public function actionClearAssets(){
       	$path = Yii::getPathOfAlias('webroot.assets');
       	$files = glob($path.DS.'*');

       	foreach($files as $one){
       		CFileHelper::removeDirectory($one);
       	}
    }

    //phpinfo
	public function actionPhpinfo(){
       	phpinfo();
    }

    /**
	 * Функция которая находит домен
	 * @return
	 */
	public function getHost(){
		if(self::$_host !== null) return self::$_host;

		$host = request()->hostInfo;
		$host = preg_replace('#(https?://)(.*)#', '$2', $host);
		$parts = explode('.', $host);

		if(count($parts) == 3) unset($parts[0]);
		self::$_host = implode('.', $parts);

		return self::$_host;
	}

	/**
	 * Функция которая находит субдомен
	 * @return
	 */
	public function getSubdomain(){

		if(self::$_subdomain !== null) return self::$_subdomain;

		$host = request()->hostInfo;
		$host = preg_replace('#(https?://)(.*)#', '$2', $host);
		$parts = explode('.', $host);
		if(count($parts) >= 2)
			self::$_subdomain = rtrim(str_replace($parts[count($parts) - 2].'.'.$parts[count($parts) - 1], '', $host), '.');
		else
			self::$_subdomain = false;

		return self::$_subdomain;
	}

}