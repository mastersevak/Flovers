<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
 
require_once dirname(dirname(__FILE__)).'/services/TwitterOAuthService.php';

class CustomTwitterService extends TwitterOAuthService {	
	
	protected function fetchAttributes() {
		$info = $this->makeSignedRequest('https://api.twitter.com/1/account/verify_credentials.json');

		//auth
		$this->attributes['service_name'] = $this->name;
		$this->attributes['service_user_id'] = $info->id;
		$this->attributes['service_user_name'] = actual($info->name;, null);
		$this->attributes['service_user_url'] = actual($info->link, null);

		//user
		$this->attributes['username'] = actual($info->name;, null);
		$this->attributes['photo'] = actual($info->profile_image_url, null);

		/* 
		все параметры которые возвращаются от yandex

		$this->attributes['language'] = $info->lang;
		$this->attributes['timezone'] = timezone_name_from_abbr('', $info->utc_offset, date('I'));
		$this->attributes['url'] = 'http://twitter.com/account/redirect_by_id?id='.$info->id_str;

	    [created_at] => 'Sat Jun 19 20:48:42 +0000 2010'
	    [profile_use_background_image] => true
	    [is_translator] => false
	    [statuses_count] => 0
	    [url] => null
	    [profile_text_color] => '333333'
	    [id_str] => '157441775'
	    [follow_request_sent] => false
	    [utc_offset] => null
	    [default_profile_image] => true
	    [name] => 'alikmanukian'
	    [lang] => 'en'
	    [notifications] => false
	    [profile_sidebar_border_color] => 'C0DEED'
	    [favourites_count] => 0
	    [friends_count] => 0
	    [screen_name] => 'alikmanukian'
	    [protected] => false
	    [profile_image_url_https] => 'https://si0.twimg.com/sticky/default_profile_images/default_profile_1_normal.png'
	    [location] => null
	    [profile_background_tile] => false
	    [followers_count] => 0
	    [profile_sidebar_fill_color] => 'DDEEF6'
	    [following] => false
	    [verified] => false
	    [default_profile] => true
	    [profile_background_color] => 'C0DEED'
	    [contributors_enabled] => false
	    [time_zone] => null
	    [profile_image_url] => 'http://a0.twimg.com/sticky/default_profile_images/default_profile_1_normal.png'
	    [profile_background_image_url_https] => 'https://si0.twimg.com/images/themes/theme1/bg.png'
	    [profile_background_image_url] => 'http://a0.twimg.com/images/themes/theme1/bg.png'
	    [listed_count] => 0
	    [description] => ''
	    [profile_link_color] => '0084B4'
	    [geo_enabled] => false
		*/
	}
}