<?php

//include elasticsearch autoload
//require_once(__DIR__.'/protected/extensions/YiiElasticSearch/vendor/autoload.php');


// change the following paths if necessary
$yii = dirname(__FILE__).'/../yiiframework/';
$helpers = dirname(__FILE__).'/protected/modules/core/helpers/Globals.php';

defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV',
			(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('APPLICATION_ENV_USER')
	|| define('APPLICATION_ENV_USER',
			(getenv('APPLICATION_ENV_USER') ? getenv('APPLICATION_ENV_USER') : 'production'));

defined('ADMIN_PATH')
	|| define('ADMIN_PATH',
			(getenv('ADMIN_PATH') ? getenv('ADMIN_PATH') : 'admin'));


if(APPLICATION_ENV != 'production' ||
	(isset($_GET['devel']) && $_GET['devel'] == 'true')){

	error_reporting( E_ALL );
	ini_set('display_errors', '1');
	//YII DEBUG on
	defined('YII_DEBUG') or define('YII_DEBUG', true);
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
}

date_default_timezone_set( (APPLICATION_ENV == 'devel' ? 'Asia/Yerevan' : 'Europe/Moscow') );

$config = dirname(__FILE__).'/protected/config/'.APPLICATION_ENV.'.php';

$yii .= APPLICATION_ENV == 'production' ? 'yiilite.php' : 'yii.php';

require_once($yii);
require_once($helpers);

$app = Yii::createWebApplication($config);

if(APPLICATION_ENV == 'production'){
	// attaching a handler to application start
	Yii::app()->onBeginRequest = function($event){
		// starting output buffering with gzip handler
		return ob_start("ob_gzhandler");
	};
	// // attaching a handler to application end
	Yii::app()->onEndRequest = function($event){
		// releasing output buffer
		return @ob_end_flush();
	};
}

$app->run();
