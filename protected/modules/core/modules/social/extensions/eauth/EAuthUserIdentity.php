<?php
/**
 * EAuthUserIdentity class file.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * EAuthUserIdentity is a base User Identity class to authenticate with EAuth.
 * @package application.extensions.eauth
 */
class EAuthUserIdentity extends CBaseUserIdentity {

	const ERROR_NOT_AUTHENTICATED = 3;

	/**
	 * @var EAuthServiceBase the authorization service instance.
	 */
	protected $service;

	/**
	 * @var string the unique identifier for the identity.
	 */
	protected $id;

	/**
	 * @var string the display name for the identity.
	 */
	protected $name;

	/**
	 * Constructor.
	 * @param EAuthServiceBase $service the authorization service instance.
	 */
	public function __construct($service) {
		$this->service = $service;
	}

	/**
	 * Authenticates a user based on {@link service}.
	 * This method is required by {@link IUserIdentity}.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		if ($this->service->isAuthenticated) {
			
			$this->id = $this->service->getAttribute('service_user_id');
			$this->name = $this->service->getAttribute('service_name');

			// You can save all given attributes in session.
			$attributes = $this->service->getAttributes();

			//смотрим есть ли на данного пользователя акаунт
			$criteria = new SDbCriteria;
			$criteria->compare('service_name', $attributes['service_name']);
			$criteria->compare('service_user_id', $attributes['service_user_id']);

			$criteria->with = array('user');
			$criteria->together = true;
			$criteria->compare('user.status', User::STATUS_ACTIVE);

			$userAuth = UserAuth::model()->find($criteria);

			//если пользователь зашел под данным ником в первый раз, 
			//то зарегистрируем его как пользователя
			if(!$userAuth){
				//регистрация
				$user = User::createAuthUser($attributes); 
			}
			else{ // зафиксируем время входа
				$user = $userAuth->user;
				$user->last_visit = time();
            	$user->update(array('last_visit'));
			}

			Yii::app()->user->UpdateInfo($user);

			$this->errorCode = self::ERROR_NONE;
		}
		else {
			$this->errorCode = self::ERROR_NOT_AUTHENTICATED;
		}
		return !$this->errorCode;
	}

	/**
	 * Returns the unique identifier for the identity.
	 * This method is required by {@link IUserIdentity}.
	 * @return string the unique identifier for the identity.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns the display name for the identity.
	 * This method is required by {@link IUserIdentity}.
	 * @return string the display name for the identity.
	 */
	public function getName() {
		return $this->name;
	}
}
