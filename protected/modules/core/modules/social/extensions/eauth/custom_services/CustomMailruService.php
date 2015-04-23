<?php
/**
 * An example of extending the provider class.
 *
 * @author ChooJoy <choojoy.work@gmail.com>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
 
require_once dirname(dirname(__FILE__)).'/services/MailruOAuthService.php';

class CustomMailruService extends MailruOAuthService {	

	protected function fetchAttributes() {
		$_info = (array)$this->makeSignedRequest('http://www.appsmail.ru/platform/api', array(
			'query' => array(
				'uids' => $this->uid,
				'method' => 'users.getInfo',
				'app_id' => $this->client_id,
			),
		));
		
		$info = $_info[0];

		//auth
		$this->attributes['service_name'] = $this->name;
		$this->attributes['service_user_id'] = $info->uid;
		$this->attributes['service_user_url'] = actual($info->link, null);

		//user
		$this->attributes['email'] = $this->attributes['service_user_email'] = actual($info->email, null);
		if($info->has_pic == 1)
			$this->attributes['photo'] = $this->attributes['service_user_pic'] = $info->pic_big;
		
		//profile
		$this->attributes['firstname'] = actual($info->first_name, null);
		$this->attributes['lastname'] = actual($info->last_name, null);

		if(isset($info->sex))
			$this->attributes['gender'] = $info->sex == 'female' ? User::GENDER_FEMALE : User::GENDER_MALE;
		if(isset($info->birthday))
			$this->attributes['birthday'] = date('Y-m-d', strtotime($info->birthday));

		/*
		все параметры которые возвращаются от facebook
		$info['sex']
		$info['birthday']
		$info['status_text']
		*/
	}
	
}
