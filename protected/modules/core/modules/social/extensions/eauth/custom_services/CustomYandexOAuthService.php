<?php
/**
 * Yandex OAuth class with "state" field support. 
 * See https://github.com/Nodge/yii-eauth/pull/21 for more details.
 *
 * @author errRust https://github.com/errRust
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
require_once dirname(dirname(__FILE__)) . '/services/YandexOAuthService.php';

class CustomYandexOAuthService extends YandexOAuthService {

	protected function fetchAttributes() {
		$info = (array) $this->makeSignedRequest('https://login.yandex.ru/info');

		list($lastName, $firstName) = explode(' ', $info['real_name']);

		//auth
		$this->attributes['service_name'] = $this->name;
		$this->attributes['service_user_id'] = $info['id'];
		$this->attributes['service_user_name'] = actual($info['display_name'], null);

		//user
		$this->attributes['username'] = actual($info['display_name'], null);
		$this->attributes['email'] = $this->attributes['service_user_email'] = actual($info['default_email'], null);
		
		//profile
		$this->attributes['firstname'] = actual($firstname, null);
		$this->attributes['lastname'] = actual($lastName, null);
		if(isset($info['sex']))
			$this->attributes['gender'] = $info->sex == 'male' ? User::GENDER_MALE : User::GENDER_FEMALE;
		if(isset($info['birthday']))
			$this->attributes['birthday'] = date('Y-m-d', strtotime($info['birthday']));

		/* 
		все параметры которые возвращаются от yandex
		
		$this->attributes['access_token'] = $this->access_token;
		$this->attributes['identifier'] = $info['id'];
		$this->attributes['sex'] = ($info['sex'] == 'male') ? 'm' : 'f';
		$this->attributes['birth_dt']=$info['birthday'];
		*/
	}

}