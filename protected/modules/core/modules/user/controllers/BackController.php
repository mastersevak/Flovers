<?php
/**
* User Module BackController
* 
* Контроллер для управления пользователями
*/
class BackController extends BController
{    
	public $model = 'User'; //for loadModel function
	public $title = 'Управление пользователями';
	public $breadcrumbs = ['Пользователи'];
	
	//фильтры
	public function filters(){
		return CMap::mergeArray(parent::filters(), [
			'postonly + registration, ajaxlogin, changepassword'
		]);
	}

	public function init(){
		parent::init();
	}

	public function allowedActions(){
		return 'login, logout, ajaxlogin, unlock';
	}

	//экшены
	public function actions()	
	{
		return CMap::mergeArray(parent::actions(), array(
			// Создаем actions captcha.
			// Он понадобиться нам для формы регистрации (да и авторизации)
			 'captcha'=>array(
				'class'=>'core.actions.SCaptchaAction',
				'height'=>34,
				'backend'=> 'gd',
			),

			'index' => [
				'class' => 'core.modules.user.actions.IndexAction',
				'breadcrumbs' => ['Пользователи'],
				'beforeRender' => function($model, &$params){
					$criteria = new SDbCriteria;

					if(($type = Yii::app()->getRequest()->getParam('type', 'all')) || $type == ''){
						
						$blocked = Yii::app()->db->createCommand()->select('id_user')->from('{{user_block}}')->queryColumn();

						switch($type){ //показать только заблокированных пользователей
							case 'blocked':
								if(!$blocked) $blocked = '-1';
								$criteria->compare('t.id', $blocked);
								break;

							default:
								$criteria->compare('t.id !', $blocked);
								break;
						}	
						
					}


					if(!Yii::app()->request->isAjaxRequest){
						$this->layout = "//layouts/tabs";

						$this->tabs = [
							'' => t('admin', 'Все'),
							'blocked' => t('admin', 'Заблокированные')
						];
					}

					$provider = $model->search($criteria);
					$params['type'] = $type;
					$params['provider'] = $provider;
				}
			],
		));
	}


	/**
	 * метод который показывает форму с данными пользователя
	 */
	public function actionUpdate($id){

		$model = $this->loadModel($this->model, $id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		$this->pageTitle = t('user','Карточка пользователя ').'['.$model->getFullName().']';
		
		$this->breadcrumbs = [t('user', 'Пользователи')=>['back/index'], t('user','Карточка пользователя')];

		$this->tabs = [
			'main'    => t('admin', 'Общая информация'),
			'more'    => t('admin', 'Дополнительно')
			];

		if($model->is_social_user){
			$this->tabs['auth'] = t('admin', 'Данные из соц. сети');
		}

		if(isset($_POST['User']))
		{
			$model->attributes = $_POST['User'];

			if($model->save()){

				if($model->id == user()->id){ //сохранение настроек профиля
					user()->UpdateInfo($model); //обновить данные текущего пользователя
				}

				$this->refresh();
			}
				
		}

		//объязательно
		if(request()->getParam('close') == 'true')
			$this->redirect(user()->gridIndex);

		$changePasswordModel = $model;
		$changePasswordModel->scenario = 'resetPassword';
		$changePasswordModel->password = '';

		$this->layout = '//layouts/tabs';

		$this->render('update', compact('model', 'changePasswordModel'));
	}

	public function actionProfile(){
		$this->actionUpdate(user()->id);
	}

	/**
	* Метод входа на сайт
	* 
	* Метод в котором мы выводим форму авторизации
	* и обрабатываем её на правильность.
	*/
	public function actionLogin() {
		
		$this->layout = "/layouts/login"; //custom layout in user module
		$this->bodyClass = "error-body";

		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery.ui');
		$cs->registerCssFile($this->module->assetsUrl.'/css/backend.css');
		$cs->registerScriptFile($this->module->assetsUrl.'/js/backend.js');

		$mdlUser = new User('blogin'); //same as User('login')

		$this->performAjaxValidation($mdlUser);
		 
		// Проверяем является ли пользователь гостем
		// ведь если он уже зарегистрирован - формы он не должен увидеть.
		if (!user()->isGuest) {
			throw new CHttpException(403, t('user', 'You are already logined!') );
		}

		$this->signin($mdlUser);

		$this->render('login', array('model' => $mdlUser, 'action' => ''));
	}  

	/**
	* Метод входа на сайт (ajax)
	*/
	public function actionAjaxLogin() {
		
		$mdlUser = new User('blogin'); //same as User('login')

		// Проверяем является ли пользователь гостем
		// ведь если он уже зарегистрирован - формы он не должен увидеть.
		if (!user()->isGuest) {
			throw new CHttpException(403, t('user', 'You are already logined!') );
			Yii::app()->end();
		}

		$this->signin($mdlUser, true);
	}   
	
	/**
	* Метод выхода с сайта
	* 
	* Данный метод описывает в себе выход пользователя с сайта
	* Т.е. кнопочка "выход"
	*/
	public function actionLogout()
	 {
	 	user()->setState('userLocked', NULL);

		// Выходим
		user()->logout();

		// Перезагружаем страницу
		$this->redirect( user()->getReturnUrl( [param('adminHome')] ) );
	}

	/**
	 * Заблокировать вход пользователя
	 */
	public function actionLock(){

		user()->setState('userLocked', 1);

		$this->redirect( array('unlock') );
	}

	public function actionUnlock(){

		if(user()->isGuest) $this->redirect(array('/user/back/login'));

		$this->layout = "/layouts/login"; //custom layout in user module
		$this->bodyClass = "error-body";

		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($this->module->assetsUrl.'/js/backend.js');

		$mdlUser = new User('blogin');
		$mdlUser->username = user()->name;

		$this->performAjaxValidation($mdlUser);

		if(isset($_POST['User'])){
			$mdlUser->attributes = $_POST['User'];

			if($mdlUser->validate()){
				user()->setState('userLocked', NULL);

				// Перезагружаем страницу
				echo CJSON::encode(array(
						'success'=>true, 
						'redirect'=>user()->getReturnUrl( [param('adminHome')] ) ));
			}
			else {
				echo CJSON::encode(array('success'=>false, 'errors'=>$mdlUser->getErrors()));
			}

			Yii::app()->end();
		}

		$this->render('unlock', ['model'=>$mdlUser]);
	}

	/**
	 * Вход от имени другого пользователя
	 */
	public function actionLoginAs($id){
		user()->checkAccess('loginas');
		/**
		 * Проверить на права
		 * не каждый имеет право делать такое
		 */
		if(user()->loginAs($id)){
			if(request()->isAjaxRequest)
				Common::jsonSuccess(true);
			else $this->refresh();
		}
	}

	/**
	* Метод регистрации (AJAX)
	*
	* Выводим форму для регистрации пользователя и проверяем
	* данные которые придут от неё.
	*/
	public function actionRegistration() {

		$mdlUser = new $this->loadModel('User');
		$mdlUser->scenario = 'registration';
		
		$mdlUser->is_social_user = User::SOCIAL_USER_NO;

		//ajax validation
		$this->performAjaxValidation($mdlUser);

		/**
		* Если $_POST['User'] не пустой массив - значит была отправлена форма
		* следовательно нам надо заполнить $mdlUser этими данными
		* и провести валидацию.            
		*/
		if (request()->isPostRequest && !empty($_POST[$this->model])) {

			// Заполняем $mdlUser данными которые пришли с формы
			$mdlUser->attributes = $_POST[$this->model];
			$mdlUser->email_confirmed = User::EMAIL_CONFIRM_YES;

			// В validate мы передаем название сценария. Оно нам понадобиться
			// когда будем заниматься созданием правил валидации
			if($mdlUser->save()) {
				// Если валидация прошла успешно... 

				echo CJSON::encode(['success'=>true]);
			}


		 } 
	}
	
	/**
	 * Смена пароля (AJAX)
	 */
	public function actionChangePassword($id = false){

		$model = $this->loadModel($this->model, $id);
		$model->scenario = 'resetPassword';
		$model->password = '';

		$this->performAjaxValidation($model);

		//если у редактироемого пользователя уровень выше 
		/**
		 * @todo
		 */

		if(!empty($_POST[$this->model])){    //случай изменения своего собственного пароля
			//сохранение нового пароля
			$model->attributes = $_POST[$this->model];

			if($model->save()){
				echo CJSON::encode(['success'=>true]);
			}  
			else {
				echo CJSON::encode(['success' => false, 'errors' => $model->getErrors()]);
			}
			
		}

	}

	public function actionDelete($id = 0){
		$type = request()->getParam('type', 'all');

		if($type == 'blocked') {
			foreach (UserBlock::model()->findAll('id_user = :id', [':id'=>$id]) as $model)
				$model->delete();
		}
		else {
			$model = $this->loadModel($this->model, $id);
			$model->delete();
		}

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
	}

	//login 
	public function signin($model, $ajax = false){

		$this->performAjaxValidation($model);
		
		if (request()->isPostRequest && !empty($_POST[ 'User' ]) ) {
				
			$model->attributes = $_POST[ 'User' ];
			// Проверяем правильность данных
			
			$identity = new BUserIdentity($model->username, $model->password);
			$identity->authenticate();


			if($identity->errorCode == CBaseUserIdentity::ERROR_NONE) {

				// Данная строчка говорит что надо выдать пользователю
                // соответствующие куки о том что он зарегистрирован, срок действий
                 // у которых указан вторым параметром. 
                $duration = $model->rememberMe ? 3600*24*30 : 0; // 30 days

                user()->login($identity, $duration);

				// если всё ок - либо идет туда откуда запрос забросил нас сюда, либо на страницу админ
				$homeUrl = [param('adminHome')];
				
				if($ajax) {
					echo CJSON::encode(array(
						'success'=>true, 
						'redirect'=>user()->getReturnUrl( $homeUrl ) ));
					
					Yii::app()->end();
				}
					
				$this->redirect( user()->getReturnUrl( $homeUrl ) );
			} 


		} 
	}

}
