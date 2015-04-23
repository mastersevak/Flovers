<?php
/**
 * Yandex OAuth class with "state" field support. 
 * See https://github.com/Nodge/yii-eauth/pull/21 for more details.
 *
 * @author errRust https://github.com/errRust
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
require_once dirname(dirname(__FILE__)) . '/services/GoogleOAuthService.php';

class CustomGoogleOAuthService extends GoogleOAuthService {

	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo');
		
		//auth
		$this->attributes['service_name'] = $this->name;
		$this->attributes['service_user_id'] = $info['id'];
		$this->attributes['service_user_url'] = actual($info['link'], null);

		//user
		$this->attributes['email'] = $this->attributes['service_user_email'] = actual($info['email'], null);
		$this->attributes['photo'] = $this->attributes['service_user_pic'] = actual($info['picture'], null);
	
		//profile
		$this->attributes['firstname'] = actual($info['given_name'], '');
		$this->attributes['lastname'] = actual($info['family_name'], '');

		if(isset($info['gender']))
			$this->attributes['gender'] = $info['gender'] == 'female' ? User::GENDER_FEMALE : User::GENDER_MALE;
		
		if(isset($info['birthday']))
			$this->attributes['birthday'] = date('Y-m-d', strtotime($info['birthday']));
	}
}