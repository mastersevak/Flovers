<?php 
/**
 * Class for easy using cookies
 * get cookie example: $cookie = Cookie::get('cookie_name');
 * set cookie example: Cookie::set('cookie_name', 'cookie value', time()+60*60*24*180);
 * 
 * мы устанавливаем дату истечения относительно текущего времени (первый подводный камень) и в виде UNIX timestamp, а не в виде форматированных даты и времени (второй подводный камень). Потому в примере выше мы используем PHP функцию time(). Мы считаем время истечения только в секундах .	
 */
class Cookie {
	//get cookie
	public static function get ($name) {
		$cookie = request()->cookies[$name];
		if(!$cookie) return null;

		return $cookie->value;
	}

	//set cookie
	public static function set ($name, $value, $expiration = 0 ) {
		$cookie = new CHttpCookie ($name, $value);
		//при значении по умолчанию куки удалятся при закрытии браузера
		$cookie->expire = $expiration;
		$cookie->path = '/';
		request()->cookies[$name] = $cookie;
	}

	//delete cookie
	public static function delete ($name) {
		unset(request()->cookies[$name]);
	}

	//delete all cookies
	public static function clearAll () {
		request()->cookies->clear();
	}
}

 ?>