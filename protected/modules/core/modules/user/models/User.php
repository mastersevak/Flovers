<?php
/**
 * User model 
 * -------------
 * 
 * @property integer    $id                			- id
 * @property integer    $created           			- создано
 * @property integer    $changed           			- изменено
 * @property integer    $activated         			- 
 * @property integer    $last_visit        			- 
 * @property integer    $status            			- статус пользователя
 * @property string     $username          			- имя пользователя
 * @property string     $email             			- эл. почта 
 * @property string     $password          			- пароль
 * @property string     $salt              			- 
 * @property integer    $email_confirmed   			- подтвержденный email
 * @property string     $register_ip              	- 
 * @property string     $activation_ip              - 
 * @property integer 	$id_creator		   			- Кто создал
 * @property integer 	$id_changer		   			- Кто изменил
 * @property integer    $avatar            			- 
 * @property string     $reset_key         			- 
 * @property string     $activation_key    			- 
 * @property string     $api_key    	   			- 
 * @property string     $hash    		   			- 
 * @property integer    $is_social_user    			- ползователь из соц. сети 
 * @property string     $firstname         			- имя
 * @property string     $lastname          			- фамилия
 * @property string     $middlename        			- отчество
 *
 * 
 * Scopes
 * -------------
 * 
 * @scope active       -
 * @scope inactive     -
 * @scope notActivated - 
 * @scope social       - 
 * @scope noSocial     - 
 * 
 * Functions
 * --------------
 *
 * @function authenticate()          - 
 * @function generateActivationKey() -
 * @function generateSalt()          - 
 * @function generatePassword()      - 
 * @function generateHashKey()       - генерация hash ключа
 * @function generateApiKey()        - генерация api ключа
 * @function getFullName()           - полное имя пользователя
 * @function validatePassword()      - валидация пароля
 * @function changePassword()        - смена пароля
 * @function getBackUrl()            - 
 * @function activate()              - aктивация пользователя
 * @function createAuthUser()        - создание пользователя из соцсети
 * @function isSocial()              - проверяет язвляется ли пользователь из соцсети
 * @function isRole()                - проверяет должность пользователя
 * @function getApiKey()             - получаем apiKey
 * @function autocomplete()          - возвращает массив пользователей, для autocomplete
 * 
 */

class User extends AR
{        
	const EMAIL_CONFIRM_NO  = 0;
	const EMAIL_CONFIRM_YES = 1;

	const SOCIAL_USER_NO = 0;
	const SOCIAL_USER_YES = 1;

	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;
   
	// для капчи
	public $verifyCode;
	// для поля "старый пароль"
	public $old_password;
	// для поля "повтор пароля"
	public $password2;
	//поле для запоминания пароля
	public $rememberMe;

	public $fullname, $statuses; //для фильтров (только те переменные которых нет в полях)
	public $searchAttributes = ['id', 'fullname', 'statuses', 'username'];

	//поля из связанной таблицы UserAuth
	public $service_name,
		   $service_user_id,
		   $service_user_name,
		   $service_user_email,
		   $service_user_url,
		   $service_user_pic;

	//для временного хранения аватара
	public $image; 

	private static $listData; //для хранения кеша
	 
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	// отдаём соединение, описанное в компоненте db
	public function getDbConnection(){
		return Yii::app()->db;
	}
 
	// возвращаем имя таблицы вместе с именем БД
	public function tableName(){
		 return 'user';
	}

	public function getId(){
		return $this->id; //чтобы не было конфликта, с rights module
	}
	
	/**
	 * Правила валидации
	 */
	public function rules() {

		return array(

			/**
			 * Общие правила 
			 */
			
			//filter trim
			array('username, email', 'filter', 'filter' => 'trim'),
			//filter purify
			array('username, email', 'filter', 'filter' => [$this->purifier, 'purify']),

			//email
			array('email', 'email', 'message' => t('user', 'Некорректный формат эл. почты')),

			//numerical
			array('status, last_visit, id_creator, is_social_user', 'numerical', 'integerOnly' => true, 'allowEmpty'=>true),

			//match pattern 
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9\-\_\.@]{2,50}$/u',
					'message' => t('user', '{attribute} может содержать только латинские символы, цифры, и знаки -, _') ),

			//max, min
			array('username, email', 'length', 'max' => 50),

			/**
			 * Правила для update 
			 */

			//required
			// array('email', 'required', 'on'=>'update', 'message' => Yii::t('user', 'Укажите эл. почту')),

			//unique
			array('username, email', 'unique', 'on' => 'update', 
				'criteria' => array('condition' => 'is_social_user = '.self::SOCIAL_USER_NO)),
			//image
			array('image', 'file', 'types' => implode(', ', param('upload_allowed_extensions')), 'allowEmpty' => true),

			/**
			 * Правила для login 
			 */

			//required
			array('username, password', 'required', 'on' => 'blogin, flogin'),

			//custom rules
			array('password', 'authenticate', 'identity' => user()->backUserIdentity, 'on' => 'blogin', 
				'message' => Yii::t('user', 'Укажите пароль')),
			array('password', 'authenticate', 'identity' => user()->frontUserIdentity, 'on' => 'flogin',
				'message' => Yii::t('user', 'Укажите пароль')),

			//safe
			array('rememberMe', 'safe', 'on' => 'blogin, flogin'),
			array('social_service_name', 'safe', 'on' => 'search'),

			/**
			 * Правила для registration
			 */

			//required
			array('password', 'required', 'on' => 'registration, fregistration',
				'message' => Yii::t('user', 'Укажите пароль')),
			array('email', 'required', 'on' => 'fregistration',
				'message' => Yii::t('user', 'Укажите эл. почту')),

			array('username', 'required', 'on' => 'registration', 'message' => Yii::t('user', 'Укажите имя пользователя')),
		   
			//captcha
			// array('verifyCode', 'ActiveCaptchaValidator', 'allowEmpty' => !CCaptcha::checkRequirements('gd'), 
			// 		'on' => 'fregistration'),
			
			//unique
			array('email, username', 'unique', 'message' => t('user', '{attribute} {value} уже занят'), 
				'on' => 'registration, fregistration', 
				'criteria' => array('condition' => 'is_social_user='.self::SOCIAL_USER_NO),
			),
			
			//compare
			array('password2', 'compare', 'compareAttribute' => 'password', 
				'message' => t('user', 'Пароли не совпадают'),
					'on' => 'registration, fregistration, changePassword, resetPassword'),

			//reset password
			array('password, old_password', 'required', 'on' => 'changePassword'),
			array('password', 'required', 'on' => 'resetPassword'),
			array('email', 'exist', 'on' => 'resetPassword', 'allowEmpty' => false, 
				'message' => t('user','{attribute} {value} не зарегистрирован'), 
				'criteria' => array('condition' => 'is_social_user='.self::SOCIAL_USER_NO)),

			array('old_password', 'checkOldPassword', 'id' => $this->id, 'on'=> 'changePassword', 'message' => 'Ваш старый пароль неверный.'),

			/**
			 * Общие правила
			 */
			
			//filter trim
			array('firstname, lastname, middlename', 'filter', 'filter'=>'trim'),
			//filter purify
			array('firstname, lastname, middlename', 'filter', 'filter' => [$this->purifier, 'purify']),
			
			//max, min
			array('firstname, lastname, middlename', 'length', 'max'=>50),

			/**
			 * Поля из связанной таблицы UserAuth
			 */
			array('service_name, service_user_id, service_user_name, service_user_email, service_user_url, service_user_pic', 'safe'),

			//search attributes
			[$this->getSearchAttributes(), 'safe', 'on' => 'search'],
		);
	}


	public function checkOldPassword($attribute, $params){
		$password = Yii::app()->db->createCommand()
						->select('password')
						->from($this->tableName())
						->where('id = '.$params['id'])
						->queryScalar();
		if (md5($this->$attribute.$this->salt) !== $password)
			$this->addError('old_password', $params['message']);
	}

	public function relations(){
		return [
			'auth'          	=> [self::HAS_ONE, 'UserAuth', 'id_user', 'deleteBehavior'=>true],
			'photo'         	=> [self::BELONGS_TO, 'Photo', 'avatar', 'deleteBehavior'=>true],
		];
	}

	public function behaviors(){
		return CMap::mergeArray(parent::behaviors(), array(
			//для использования, getImage, getImageUrl, а так же для сохранения картинки ....
			'imageBehavior' => array(
				'class'  => 'ImageBehavior',
				'image'  => 'image',  //картинка
				'field'  => 'avatar', //поле для сохранения, ссылки на картинку
				'params' => param('images/user') //массив с настройками для картинки
			),
			'dateBehavior' => array(
				'class'           => 'DateBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'changed',
			),
            'userSearch'    => ['class' => 'core.modules.user.behaviors.UserSearchBehavior'],
			'withRelated' => array('class'=>'core.behaviors.WithRelatedBehavior'),
			'filters'         => ['class'=>'core.behaviors.FilterBehavior'],
		));
	}


	/**
	 * Функция которая возвращает масив с названиями labels для соответствующих полей
	 */
	public function attributeLabels() {
		
		return array(
			'username'           =>  t('user', 'Имя пользователя'),
			'password'           =>  t('user', 'Пароль'),
			'password2'          =>  t('user', 'Повторите пароль'),
			'old_password'       =>  t('user', 'Старый пароль'),
			'email'              =>  t('user', 'Эл. почта'),
			'verifyCode'         =>  t('user', 'Проверочный код'),
			'rememberMe'         =>  t('user', 'Запомнить меня'),
			'avatar'             =>  t('user', 'Аватарка'),
			'is_social_user'     =>  t('user', 'Из соц. сети'),
			'status'             =>  t('user', 'Статус'),
			'image'              =>  t('user', 'Аватарка'),
			
			//profile
			'fullname'           =>  t('user', 'Ф.И.О.'),
			'firstname'          =>  t('user', 'Имя'),
			'lastname'           =>  t('user', 'Фамилия'),
			'middlename'         =>  t('user', 'Отчество'),

			//more
			'created'            =>  t('user', 'Дата создания'),
			'changed'            =>  t('user', 'Дата обновления'),
			'activated'          =>  t('user', 'Дата активации'),
			'last_visit'         =>  t('user', 'Дата последнего визита'),
			'id_creator'         =>  t('user', 'Создал'),
			'id_changer'         =>  t('user', 'Изменил'),
			'registration_ip'    =>  t('user', 'Зарегистрирован с IP'),
			'activation_ip'      =>  t('user', 'Активирован с IP'),

			//auth
			'service_name'       => t('user', 'Название услуги'),
			'service_user_id'    => t('user', 'ID пользователя'),
			'service_user_name'  => t('user', 'Имя пользователя'),
			'service_user_email' => t('user', 'Эл. почта пользователя'),
			'service_user_url'   => t('user', 'Ссылка на акаунт'),
			'service_user_pic'   => t('user', 'Аватарка'),
		);
	}

	public function beforeSave() {

		if(parent::beforeSave()){

			if ($this->isNewRecord && !Common::isCLI()){

				if($this->is_social_user == self::SOCIAL_USER_NO) {
					$this->status = self::STATUS_INACTIVE;
					$this->email_confirmed = self::EMAIL_CONFIRM_NO;
					
					$this->salt = $this->generateSalt();
					$this->password = $this->generatePassword($this->password); 
					$this->activation_key = $this->generateActivationKey();

					$this->api_key = $this->generateApiKey();
				}    
				else {
					$this->status = self::STATUS_ACTIVE;
				}       
				
				$this->registration_ip = $this->activation_ip = Yii::app()->request->userHostAddress;

			}
			else{
				if($this->scenario == 'resetPassword' || $this->scenario == 'changePassword') { //если это режим восстановления пароля
					
					$this->password = $this->generatePassword($this->password);
					$this->activation_key = $this->generateActivationKey();

				}
			}
			
			return true;
		}
		
		return false;	
	}

	public function beforeDelete() {

		if(parent::beforeDelete()){
			//невозможно удалить пользователя amanukian
			if(in_array($this->username, Yii::app()->getModule('core')->getModule('user')->superusers)){
				
				Yii::log(NL."Пытались удалить супер пользователя: [{$this->id}]=>{$this->username}\n".
						 "Пользователь производивший действие: [".user()->id."]=>".user()->username."\n");
				
				return false;
			}

			return true;
		}
		
		return false;    
	}
	
	public function scopes() {
		$alias = $this->getTableAlias();

		return array(
			'active' => array(
				'condition' => "{$alias}.status = :status",
				'params'    => array(':status' => self::STATUS_ACTIVE),
			),
			'inactive' => array(
				'condition' => "{$alias}.status = :status",
				'params'    => array(':status' => self::STATUS_INACTIVE),
			),
			'notActivated' => array(
				'condition' => "{$alias}.status = :status or {$alias}.email_confirmed = :email_confirmed",
				'params'    => array(':status' => self::STATUS_INACTIVE, ':email_confirmed'=> self::EMAIL_CONFIRM_NO),
			),
			'social' => array(
				'condition' => "{$alias}.is_social_user = :social",
				'params'    => array(':social' => self::SOCIAL_USER_YES),
			),
			'noSocial' => array(
				'condition' => "{$alias}.is_social_user = :social",
				'params'    => array(':social' => self::SOCIAL_USER_NO),
			),
		);
	}

	 /**
	 * Собственное правило для проверки
	 * Данный метод является связующем звеном с UserIdentity
	 */
	public function authenticate($attribute, $params) {
		// Проверяем были ли ошибки в других правилах валидации.
		// если были - нет смысла выполнять проверку
		
		if(!$this->hasErrors())
		{
			// Создаем экземпляр класса UserIdentity
			// и передаем в его конструктор введенный пользователем логин и пароль (с формы)
			$identity = new $params['identity']($this->username, $this->password);
			 // Выполняем метод authenticate (о котором мы с вами говорили пару абзацев назад)
			// Он у нас проверяет существует ли такой пользователь и возвращает ошибку (если она есть)
			// в $identity->errorCode
			$identity->authenticate();
				
			// Теперь мы проверяем есть ли ошибка..    
			switch($identity->errorCode)
			{
				// Если ошибки нету...
				 case CBaseUserIdentity::ERROR_NONE: {
					//ошибок нет
					
					break;
				}
				case BUserIdentity::ERROR_USER_BLOCKED: {
					$this->addError('username', t('user', 'Пользователь заблокирован'));
					break;
				}
				case CBaseUserIdentity::ERROR_USERNAME_INVALID: {
					 // Если логин был указан наверно - создаем ошибку
					$this->addError('username', t('user', 'Такой пользователь не существует!'));
					break;
				}
				 case CBaseUserIdentity::ERROR_PASSWORD_INVALID: {
					// Если пароль был указан наверно - создаем ошибку
					$this->addError('password', t('user', 'Вы указали не верный пароль!'));
					 break;
				}
			}

			return $identity;
		}

		return false;
	}

	public function generateActivationKey() {
		return md5(time() . $this->email . uniqid());
	}

	public function generateSalt(){
		return String::randomString(4, '0123456789');
	}

	public function generatePassword($password){
		return md5($password.$this->salt);
		// return Password::hashPassword($password);
	}
	
	public function generateHashKey() {
		return md5(time() . $this->email . uniqid());
	}

	public function generateApiKey() {
		return md5(time() . $this->email . 'api_key'. uniqid());
	}

	public function getFullName($middle = false, $separator = ' ') {
		$name = array();

		if(!empty($this->firstname)) $name[] = $this->firstname;
		if(!empty($this->middlename) && $middle) $name[] = $this->middlename;
		if(!empty($this->lastname)) $name[] = $this->lastname;

		if(empty($name)) $name[] = $this->username;
		if(empty($name)) $name[] = $this->email;

		return implode($separator, $name);
	}

	public function validatePassword($password, $hashedPassword = false) {
		if($hashedPassword) return $password == $this->password;
		
		return md5($password.$this->salt) == $this->password;
		// return Password::verifyPassword($password, $this->password);
	}

	public function changePassword($password){
		$this->password = Password::hashPassword($password);

		return $this->update(array('password'));
	}

	public function getBackUrl(){
		return app()->createUrl('/core/user/back/update', array('id'=>$this->id));
	}

	/**
	 * Активация пользователя
	 */
	public function activate() {
		$this->activation_ip     = Yii::app()->request->userHostAddress;
		$this->activated         = time();
		$this->status            = self::STATUS_ACTIVE;
		$this->email_confirmed   = self::EMAIL_CONFIRM_YES;

		return $this->saveAttributes(array('activation_ip', 'activated', 'status', 'email_confirmed'));
	}

	/**
	 * Создание пользователя из соцсети
	 */
	public static function createAuthUser($attributes) {
		
		$user = new User;
		$auth = new UserAuth;

		$user->attributes = $attributes;
		$auth->attributes = $attributes;

		$user->setAttributes(array(
			'created'             => date('Y-m-d H:i:s'),
			'changed'             => date('Y-m-d H:i:s'),
			'last_visit'          => time(),
			'registration_ip'     => Yii::app()->request->userHostAddress,
			'status'              => self::STATUS_ACTIVE,
			'is_social_user'      => self::SOCIAL_USER_YES,
		));

		$auth->setAttributes(array(
			'created' => date('Y-m-d H:i:s'),
			'changed' => date('Y-m-d H:i:s'),
		));

		if($attributes['photo']){
			$photo = new Photo;
			$photo->filename = String::randomString(12);
			$user->avatar = $photo->uploadImage($attributes['photo'], param('images/user'));
		   
			$auth->service_user_pic = $attributes['photo'];
		}

		$user->auth = $auth;

		if($user->withRelated->save(false, array('auth'))){
			return $user;
		}
		else{
			throw new Exception('Пользователь не создался ... ');
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($criteria = false)
	{
		if(!$criteria) $criteria = new SDbCriteria;
		
		// $criteria->compare('t.id !', user()->id); //исключаем самого себя
		$criteria->compare('t.id', $this->id);
		$criteria->compare('username', $this->username, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('is_social_user', $this->is_social_user);

		$criteria->compare('firstname', $this->firstname, true);
		$criteria->compare('lastname', $this->lastname, true);
		$criteria->compare('middlename', $this->middlename, true);
		
		$this->compareUser($criteria, $this->fullname, 't');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
			  'pageSize'=>Common::getPagerSize(__CLASS__),
			  'pageVar' => 'page'
			),
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
				'attributes'=>array(
					'fullname'=>array(
						'asc' => 'firstname, lastname, middlename, username',
						'desc' => 'firstname DESC'
						),
					'username',
					'email',
					'status'
				)
			),
		));
	}

	public function isSocial(){
		return $this->is_social_user == self::SOCIAL_USER_YES;
	}

	public function isRole($role){ 

		$item = app()->authManager->getAuthItem($role);
		if(!$item || $item->type != CAuthItem::TYPE_ROLE) return false;

		if(app()->authManager->isAssigned($role, $this->id))
			return true;
		
		$roles = Yii::app()->authManager->getRoles($this->id);
		foreach($roles as $roleName=>$roleInfo){
			if(app()->authManager->hasItemChild($roleName, $role))
				return true;
		}

		return false;
	}

	//получаем apiKey, для пользователя с заданным username, password
	public function getApiKey($username, $password){

		//тут используется та же логика что при авторизации
		$criteria = new SDbCriteria;

		$criteria->compare(String::isEmail($username) ? 'email' : 'username', $username);

		$user = User::model()
					->noSocial()
					->active()
					->find($criteria);

		if($user && $user->validatePassword($password)){
			return $user->api_key;
		}

		return false;
	}

	public static function autocomplete($keyword, $limit = 10){
		$models=self::model()->findAll(array(
			'condition'=>'firstname LIKE :keyword OR lastname LIKE :keyword',
			'order'=>'firstname',
			'limit'=>$limit,
			'params'=>array(':keyword'=>"%$keyword%")
		));
		$suggest=array();
		
		foreach($models as $model) {
			$suggest[] = array(
				'label'=>$model->fullName,    // label for dropdown list
				'value'=>$model->fullName,    // value for input field
				'id'=>$model->id,             // return values from autocomplete
			);
		}
		return $suggest;
	}

	/**
     * list Data
     * возвращяет масив ['id' => 'name'] для dropDownList и сохраняет в кеш
     */
	public static function listData(){
		if(self::$listData) return self::$listData;

		$cacheKey = "listdata.User";
		
		if(self::$listData = Yii::app()->cache->get($cacheKey))
			return self::$listData;

		$table = self::model()->tableName();

		self::$listData = CHtml::listData(Yii::app()->db->createCommand()->
									select("id, CONCAT(firstname, ' ', lastname) as name")->
									from($table)->queryAll(), 'id', 'name');

		$dependency = new CDbCacheDependency($sql = "SELECT MAX(changed) FROM ".$table);
		Yii::app()->cache->set($cacheKey, self::$listData, 0, $dependency);
	
		return self::$listData;
	}

	public static function getUserFromCache($id){
		if(!self::$listData){
			self::$listData = self::listData();
			
		}
		if(isset(self::$listData[$id])) return self::$listData[$id];

		return '';
	}
}