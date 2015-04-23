<?php 
/**
* Теперь вы можете использовать ваши глобальные функции в любом месте вашего приложения. 
* Например, что бы получить доступ к данным пользователя мы можем использовать user() 
* вместо app()->user
* 
* Ниже приведен код, содержащий список наиболее используемых функций быстрого доступа.
 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('BR') or define('BR', '<br>');
defined('NL') or define('NL', "\n");

/**
* Быстрый доступ к app()
*/
function app() {
	return Yii::app();
}

/**
* Быстрый доступ к app()->createUrl()
*/
function url($route, $params=array(), $ampersand='&') {
	return app()->createUrl($route, $params, $ampersand);
}

/**
* Создает url для перехода с фильтрами
*/
function filterUrl($route, $modelName, $params) {
	$parsedParams = [];

	foreach($params as $attribute => $value){
        if(is_array($value)) $parsedParams[] = $modelName.'['.$attribute.'][]='.$value[0];
        else $parsedParams[] = $modelName.'['.$attribute.']='.$value;  
    }

	return app()->createUrl($route).'/?'.implode('&', $parsedParams);
}

/**
* Быстрый доступ к CHtml::encode
*/
function h($text) {
	return htmlspecialchars($text, ENT_QUOTES, app()->charset);
}

/**
* Быстрый доступ к CHtml::link()
*/
function l($text, $url = '#', $htmlOptions = array()) {
	return CHtml::link($text, $url, $htmlOptions);
}

/**
* Быстрый доступ к Yii::t(). По умолчанию, используется категория 'stay'
*/
function t($category, $message, $params = array(), $source = null, $language = null) {
	return Yii::t($category, $message, $params, $source, $language);
}

/**
* Быстрый доступ к Yii::log().
*/
function lg($msg, $level='info', $category='application') {
	return Yii::log($msg, $level, $category);
}

/**
* Быстрый доступ к  app()->request->baseUrl
* Если вы используете параметр $url - он будет добавлен как префикс к baseUrl.
*/
function bu($url=null) {
	static $baseUrl;
	if ($baseUrl===null)
		$baseUrl = app()->request->baseUrl;
	if (empty($baseUrl))
		$baseUrl = '/';
	$baseUrl = $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');

	if(substr($baseUrl, -1) != '/') $baseUrl .= '/'; //append / if not /
	
	return $baseUrl;
}

/**
 * Быстрый доступ к app()->params[$name].
 * можно обращаться к вложенным значениям так: param('settings/adminEmail');
 */
function param($name) {
	$parts = explode('/', $name);
	$params = Yii::app()->params;

	foreach($parts as $part){
		$params = isset($params[$part]) ? $params[$part] : false;
	}
	
	return $params;
}

/**
* Быстрый доступ к app()->user.
*/
function user() {
	return app()->user;
}

/**
* Быстрый доступ к app()->clientScript
*/
function cs() {
	return app()->clientScript;
}

function request() {
	return app()->request;
}

function db(){
	return app()->db;
}

function redirect($url, $terminate=true, $statusCode=302) {
	return app()->request->redirect($url, $terminate, $statusCode);
}

function lang() {
	return app()->language;
}

function dump($var, $exit = false, $highlight=true, $depth=10) {
	CVarDumper::dump($var, $depth, $highlight);
	if($exit) Yii::app()->end();
}

function sdump($var, $highlight=true, $depth=10) {
	return CVarDumper::dumpAsString($var, $depth, $highlight);
}

function exception($code, $message = false) {
	if($message)
		throw new CHttpException ($code, $message);
	else 
		throw new CHttpException ($code);
}

function actual(&$val1, $val2 =false){
	return (isset($val1) && !empty($val1)) ? $val1 : $val2;
}

function encode($text){
	return CHtml::encode($text);
}

function cfile($filename = false){
	if($filename)
		return app()->file->set($filename);
	else 
		return app()->file;
}

function assets($path = false, $hashByName = false, $level = -1, $forceCopy = NULL){
	if(!$path) return app()->getAssetManager();
	return app()->getAssetManager()->publish($path, $hashByName, $level, $forceCopy);
}

function image($filename = false){
	if(!$filename) return app()->image;
	return app()->image->load($filename);
}

function setFlash($key, $message){
	$txt = Block::getBlock($message);
    if(!$txt) $txt = t('flash', $message);

    Yii::app()->user->setFLash($key, $txt);
}

function cookie($key, $value = false, $options = array()){
	if(!$value){ //get
		return request()->cookies[$key];
	}
	else{ //set
		request()->cookies[$key] = new CHttpCookie($key, $value, $options);
	}
}

function isSerializable ($value) {
  $return = true;
  $arr = array($value);

  array_walk_recursive($arr, function ($element) use (&$return) {
    if (is_object($element) && get_class($element) == 'Closure') {
      $return = false;
    }
  });

  return $return;
}

//Signature: array array_column ( array $input , mixed $column_key [, mixed $index_key ] )
if(!function_exists('array_column')){
    function array_column( array $input, $column_key, $index_key = null ){
        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        
        return $result;
    }
}

/**
 * Get the current IP of the connection
 * 
 */
function ip() {
    if (isset($_SERVER)) {
    if(isset($_SERVER['HTTP_CLIENT_IP'])){
    $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])){
    $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else{
    $ip = $_SERVER['REMOTE_ADDR'];
    }
    }
    else
    {
    if (getenv( 'HTTP_CLIENT_IP')) {
    $ip = getenv( 'HTTP_CLIENT_IP' );
    }
    elseif (getenv('HTTP_FORWARDED_FOR')) {
    $ip = getenv('HTTP_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_X_FORWARDED_FOR')) {
    $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    else {
    $ip = getenv('REMOTE_ADDR');
    }
    }
    return $ip;
}

/**
* Get array of subfolders name
*/
function get_subfolders_name($path,$file=false){
    
    $list=array();    
    $results = scandir($path);
    foreach ($results as $result) {     
        if ($result === '.' or $result === '..' or $result === '.svn') continue;
        if(!$file) {
            if (is_dir($path . '/' . $result)) {
                $list[]=trim($result);
            }
        }
        else {          
            if (is_file($path . '/' . $result)) {
                $list[]=trim($result);
            }
        }
    }
    
    return $list;
}

/**
* Check current app is console or not
*/
function isConsoleApp() {
    return get_class(Yii::app())=='CConsoleApplication';
}

//XSS Clean Data Input from Litpi.com
 function xss_clean($data)
{
    return $data;
    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('cms','Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    // we are done...
    return $data;
}

function plaintext($s)
{
    $s = strip_tags($s);
    $s = xss_clean($s);
    return $s;
}

function isValidURL($url)
{
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

?>