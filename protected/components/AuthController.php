<?php
/**
* AuthController
* 
* Контроллер для наших пользователей. Содержит в себе следующие функции:
* - авторизация
* - регистрация
* - выход
* - редактирование профиля [в будущем]
*/
Yii::import('app.controllers.FrontController');
class AuthController extends FrontController
{     
	public $model = 'User'; 
	public $tabs = [];

	public function filters()
	{
		return [
			'accessControl + profile, changepassword',
		];
	}

	//Правила доступа, используемые фильтром определяются переопределением метода
	public function accessRules()
	{
		return [
			//даем доступ только зарегистрированным пользователям
			[
				'allow',
				'users' => ['@'],
			],
			[
				'deny',
				'users' => ['*']
			]
		];
	}

	public function actions()
	{
		return [
			// Создаем actions captcha.
			// Он понадобиться нам для формы регистрации (да и авторизации)
			'captcha' => [
				'class' => 'core.actions.SCaptchaAction',
				'height' => 42,
				'backend' => 'gd',
			],
		];
	}
	/**
	* Метод входа на сайт
	* 
	* Метод в котором мы выводим форму авторизации
	* и обрабатываем её на правильность.
	*/
	public function actionLogin() {
		// Проверяем является ли пользователь гостем
		// ведь если он уже зарегистрирован - формы он не должен увидеть.
		if(!user()->isGuest) 
			$this->redirect(param('home'));

		$mdlLogin = new LoginForm('flogin');
		$mdlReg = $this->loadModel('RegisterForm', false, ['profile']);
		$mdlReg->setScenario('fregistration');

		$scenario = request()->getParam('scenario');

		if($scenario == 'flogin')
			$this->performAjaxValidation($mdlLogin);
		elseif($scenario == 'fregistration')
			$this->performAjaxValidation($mdlReg);

		
		if(request()->isPostRequest) {
			//login
			if($scenario == 'flogin'){

				if($this->login($mdlLogin)){
					// если всё ок - либо идет туда откуда запрос забросил нас сюда, либо на страницу админ
					$this->redirect(user()->getReturnUrl(app()->createUrl(param('home'))));
				}

			} //registration
			elseif($scenario == 'fregistration'){
				$mdlReg->attributes = $_POST['RegisterForm'];

				$mdlReg->is_social_user = User::SOCIAL_USER_NO;

				if($mdlReg->save(true)) {
					// отправка email с просьбой активировать аккаунт
					$status = 
						app()->mail->send( $mdlReg->email, 
							t('front', 'Регистрация на сайте {site}!', array('{site}' => app()->name)),
							app()->mail->getView('auth.register-confirmation', array(
									'username' => $mdlReg->username, 
									'key' => $mdlReg->activation_key
									))); 

					if($status){
						//set flash message about registration complete
						setFlash('success', "<h3>Регистрация прошла успешно!</h3><p>Пожалуйста проверьте почту для активации аккаунта.</p><p>Спасибо за регистрацию.</p>");
					}
					else{
						setFlash('error', "<h3>Ошибка регистрации</h3><p>По какой то причине регистрация не удалась. Попробуйте позже.</p>");
					}

					$this->refresh();
				}
			}
		} 

		$this->render('login', array('mdlLogin' => $mdlLogin, 'mdlReg' => $mdlReg));
	}  

	/**
	 * Фунция авторизации через ajax
	 * @return bool
	 */
	public function actionAjaxLogin(){
		
		$model = new LoginForm('flogin');

		$this->performAjaxValidation($model);

		if($this->login($model)){
			Common::jsonSuccess();
		}
		else{
			Common::jsonError('Ошибка авторизации');
		}
	}

	public function login($model){
		//проверяем вход через социальные сервисы
		$service = Yii::app()->request->getQuery('service');

		if (isset($service) && $service) {
			$authIdentity = Yii::app()->eauth->getIdentity($service);

			$authIdentity->redirectUrl = user()->getReturnUrl( bu() );
			$authIdentity->cancelUrl = $this->createAbsoluteUrl('/auth/login');

			if ($authIdentity->authenticate()) {
				
				$identity = new EAuthUserIdentity($authIdentity);

				// successful authentication
				if ($identity->authenticate()) {
					Yii::app()->user->login($identity);

					app()->shoppingCart->saveToCart();
					app()->shoppingCart->clear();
					app()->shoppingCart->getFromDb();

					// special redirect with closing popup window
					$authIdentity->redirect();

					return true;
				}
				else {
					// close popup window and redirect to cancelUrl
					$authIdentity->cancel();

					return false;
				}
			}            
		}
		else{
			$model->attributes = $_POST[ 'LoginForm' ];
			// Проверяем правильность данных

			$identity = new FUserIdentity($model->username, $model->password);
			$identity->authenticate();

			if($identity->errorCode == CBaseUserIdentity::ERROR_NONE) {
			   
				// Данная строчка говорит что надо выдать пользователю
				// соответствующие куки о том что он зарегистрирован, срок действий
				// у которых указан вторым параметром. 
				$duration = $model->rememberMe ? 3600*24*30 : 0; // 30 days
			   
				user()->login($identity, $duration);

				app()->shoppingCart->saveToCart();
				app()->shoppingCart->clear();
				app()->shoppingCart->getFromDb();

				return true;
			} 
		}
	}
	
   /**
	* Метод выхода с сайта
	* 
	* Данный метод описывает в себе выход пользователя с сайта
	* Т.е. кнопочка "выход"
	* @return 
	*/
	public function actionLogout()
	 {
		// Выходим
		user()->logout();
		// Перезагружаем страницу
		$this->redirect(['site/index']);
	}
	
	/**
	 * Метод активации пользователя
	 * он доступен по переходу по ссылке, которую пользователь получил 
	 * по емаил.
	 * 
	 * ссылка имеет следующий вид http://host/activate/username/activationcode
	 * @param  String $username Имя пользователя
	 * @param  String $key      Ключ активации, который был выслан ему по почте
	 * @return 
	 */
	public function actionActivate($username, $key) {
		$this->pageTitle = t('front', "Активация");
		
		$user = User::model()->notActivated()->find('username = :username && activation_key =:key', 
						[':username'=>$username, ':key' => $key]);

		if(!$user){
			//user not found, or blocked, or already activated
			setFlash('error', "<h3>Ошибка активации</h3><p>Активация не прошла! Возможно ваш аккаунт уже активирован! Попробуйте позже.</p>");

			$this->render('activation');
			return;
		}

		if($user->activate()){// процедура активации
			// отправить сообщение о активации аккаунта
			$status = app()->mail->send($user->email, 
			t('front', 'Акаунт на сайте {site} активирован!', ['{site}' => app()->name]),
			app()->mail->getView('auth.account-activated', [
				'username' => $user->username, 
				'key' => $user->activation_key
			]));
			
			//set flash message for success
			setFlash('success', "<h3>Успешная активация</h3><p>Активация прошла успешно! Теперь вы можете войти на сайт используя свой логин и пароль!</p>");
		}
		else{ //error on activation process
			//set flash for activation error
			user()->setFlash('error', "<h3>Ошибка активации</h3><p>Активация не прошла! Возможно ваш аккаунт уже активирован! Попробуйте позже.</p>");
		}

		$this->render('activation');
	} 

	/**
	 * Функция отправки письма для сброса пароля
	 * @return 
	 */
	public function actionForgotPassword(){
	   
		$model = new User('resetPassword');

		//$this->performAjaxValidation($model);

		if(isset($_POST['User'])){

			$model->attributes = $_POST['User'];

			if(!empty($model->email)){ //посылка письма для сброса

				if($model->validate(['email'])){
					$user = User::model()->find('email = :email', [':email'=>$model->email]);
					$username = $user->username;
					$key = String::getUniqueString('User', 'reset_key', 12);

					// отправить сообщение для сброса пароля
					$status = 
						app()->mail->send( $user->email, 
							t('front', 'Сброс пароля на сайте {site}!', ['{site}' => app()->name]),
							app()->mail->getView('auth.reset-password', [
								'username'=>$username, 
								'key'=>$key
							]));

					//save reset key in db
					User::model()->updateByPk($user->id, ['reset_key' => $key]);

					//set user flash message
					setFlash('email-sent', 'forgot-password-success');
				}
			}
		}

		$this->pageTitle = t('user', 'Смена пароля');

		$this->render('forgotpassword', compact('model'));
	}

	/**
	 * Функция которая сбрасывает пароль на новый
	 * @param  String $username Имя пользователя
	 * @param  String $key      Ключ для сброса пароля, который был выслан ему по почте
	 * @return 
	 */
	public function actionResetPassword($username, $key){
	   
		$criteria = new CDbCriteria;
		$criteria->compare('username', $username);
		$criteria->compare('is_social_user', User::SOCIAL_USER_NO);
		$criteria->compare('reset_key', $key);

		$model = Person::model()->find($criteria);
		if(!$model) exception(404);
		$model->scenario = 'resetPassword';
		$model->password = '';
 
		$this->performAjaxValidation($model);

		if(isset($_POST['Person'])){

			$model->attributes = $_POST['Person'];
			//save new password and salt
			if($model->save()){
				//set flash message
				setFlash('password-changed', 'Password successfully changed. Now you can login with your new password.');
			}
		}

		$this->pageTitle = t('user', 'Смена пароля');

		$this->render('forgotpassword', compact('model'));
	}

	public function actionProfile(){
		if(user()->isGuest) throw new CHttpException(404, 'The requested page does not exist.');

		$model = $this->loadModel('Person', user()->id, ['profile']);

		$this->performAjaxValidation($model);
		
		if(request()->getPost('Person')){
			
			$model->setAttributes($_POST);

			if($model->save()){

				//сохранение настроек профиля
				if($model->id == user()->id)
					user()->UpdateInfo($model); //обновить данные текущего пользователя

				$this->refresh();
			}
		}

		$changePasswordModel = $model;
		$changePasswordModel->scenario = 'resetPassword';
		$changePasswordModel->password = '';

		$this->tabs = ['about' => t('back', 'About'), 'products' => 'Products', 'orders' => 'orders'];
		if(request()->isAjaxRequest && request()->getParam('ajax'))
			$this->renderPartial('_list');
		else
			$this->render('/auth/profile', compact('model', 'changePasswordModel'));
	}

	//смена пароля
	public function actionChangePassword(){
		$model = $this->loadModel('Person', user()->id);
		$model->scenario = 'changePassword';
		$model->password = null;
 
		$this->performAjaxValidation($model);

		if(isset($_POST['Person'])){

			$model->attributes = $_POST['Person'];

			if($model->save()){
				Common::jsonSuccess(true);
			} 
			else{
				Common::jsonSuccess(true, ['success' => false, 'errors' => $model->getErrors()]);
			}
		}

		$this->pageTitle = t('user', 'Смена пароля');

		$this->render('changepassword', compact('model'));
	}

	//удаление акаунта
	public function actionDelete(){
		$id = user()->id;
		$user = $this->loadModel($this->model, $id);

		$user->delete();

		user()->logout();
		$this->redirect(['/site/index']);
	}
   
} 


