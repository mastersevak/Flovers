<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)).'/services/VKontakteOAuthService.php';

class CustomVKontakteService extends VKontakteOAuthService {

	// protected $scope = 'friends';

	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://api.vk.com/method/users.get.json', array(
			'query' => array(
				'uids' => $this->uid,
				// 'fields' => 'uid, first_name and last_name' // is always available
				'fields' => 'nickname, first_name, last_name, sex, email, bdate, city, country, timezone, photo, photo_medium, photo_big, photo_rec',
			),
		));

		$info = $info['response'][0];

		//auth
		$this->attributes['service_name'] = $this->name;
		$this->attributes['service_user_id'] = $info->uid;
		$this->attributes['service_user_name'] = actual($info->nickname, null);
		$this->attributes['service_user_url'] = actual($info->url, null);

		//user
		$this->attributes['username'] = actual($info->nickname, null);
		$this->attributes['email'] = $this->attributes['service_user_email'] = actual($info->email, null);

		$this->attributes['photo'] = $this->attributes['service_user_pic'] = actual($info->photo_big, null);
		
		//profile
		$this->attributes['firstname'] = actual($info->first_name, null);
		$this->attributes['lastname'] = actual($info->last_name, null);
		if(isset($info->sex))
			$this->attributes['gender'] = $info->sex == 1 ? User::GENDER_FEMALE : User::GENDER_MALE;
		
		/* 
		все параметры которые возвращаются от vkontakte


		$this->attributes['photo_medium'] = $info->photo_medium;
		$this->attributes['photo_big'] = $info->photo_big;
		$this->attributes['photo_rec'] = $info->photo_rec;
		$this->attributes['photo'] = $info->photo;

		$this->attributes['name'] = $info->first_name.' '.$info->last_name;
		$this->attributes['url'] = 'http://vk.com/id'.$info->uid;

		$this->attributes['gender'] = $info->sex == 1 ? 'F' : 'M';

		$this->attributes['city'] = $info->city;
		$this->attributes['country'] = $info->country;

			$this->attributes['timezone'] = timezone_name_from_abbr('', $info->timezone*3600, date('I'));;

		*/


	}
}
