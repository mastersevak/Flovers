<?php
class CustomFacebookService extends FacebookOAuthService
{
	/**
	 * https://developers.facebook.com/docs/authentication/permissions/
	 */
	protected $scope = 'user_birthday, user_hometown, user_location';

	/**
	 * http://developers.facebook.com/docs/reference/api/user/
	 * @see FacebookOAuthService::fetchAttributes()
	 */
	protected function fetchAttributes() {
		$info = (object) $this->makeSignedRequest('https://graph.facebook.com/me?fields=id,username,email,first_name,last_name,picture');

		// dump($info, true);
		//auth
		$this->attributes['service_name'] = $this->name;
		$this->attributes['service_user_id'] = $info->id;
		$this->attributes['service_user_name'] = actual($info->username, null);
		$this->attributes['service_user_url'] = actual($info->link, null);

		//user
		$this->attributes['username'] = actual($info->username, null);
		$this->attributes['email'] = $this->attributes['service_user_email'] = actual($info->email, null);

		//$this->attributes['photo'] = "https://graph.facebook.com/{$info->id}/picture?width=200";
		if(is_string($info->picture))
			$this->attributes['photo'] = $this->attributes['service_user_pic'] = $info->picture;
		elseif(!$info->picture->data->is_silhouette)
			$this->attributes['photo'] = $this->attributes['service_user_pic'] = $info->picture->data->url;
		
		//profile
		$this->attributes['firstname'] = actual($info->first_name, null);
		$this->attributes['lastname'] = actual($info->last_name, null);
		$this->attributes['middlename'] = actual($info->middle_name, null);
		if(isset($info->gender))
			$this->attributes['gender'] = $info->gender == 'female' ? User::GENDER_FEMALE : User::GENDER_MALE;
		if(isset($info->birthday))
			$this->attributes['birthday'] = date('Y-m-d', strtotime($info->birthday));

		/* 
		все параметры которые возвращаются от facebook

		$this->attributes['id'] - The user's Facebook ID
		$this->attributes['name'] - The user's full name
		$this->attributes['first_name'] - The user's full name
		$this->attributes['middle_name'] - The user's middle name
		$this->attributes['last_name'] - The user's last name
		$this->attributes['gender'] - The user's gender: female or male
		$this->attributes['locale'] - The user's locale
		$this->attributes['languages'] - The user's languages (array of objects containing language id and name)
		$this->attributes['link'] - The URL of the profile for the user on Facebook
		$this->attributes['username'] - The user's Facebook username
		$this->attributes['birthday'] - The user's birthday
		$this->attributes['email'] - The proxied or contact email address granted by the user
		$this->attributes['picture'] - The URL of the user's profile pic (only returned if you explicitly specify a 'fields=picture' param)
		$this->attributes['quotes'] - Status message
		*/
	}
}
