<?php
date_default_timezone_set('Europe/Moscow');

//include elasticsearch autoload
//require_once(__DIR__.'/extensions/YiiElasticSearch/vendor/autoload.php');

defined('APPLICATION_ENV') 
 	|| define('APPLICATION_ENV', 
 			(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('APPLICATION_ENV_USER') 
 	|| define('APPLICATION_ENV_USER', 
 			(getenv('APPLICATION_ENV_USER') ? getenv('APPLICATION_ENV_USER') : 'production'));

defined('ADMIN_PATH') 
	|| define('ADMIN_PATH', 
			(getenv('ADMIN_PATH') ? getenv('ADMIN_PATH') : 'admin'));
	
$helpers = dirname(__FILE__).'/modules/core/helpers/Globals.php';
require_once($helpers);


// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../yiiframework/yiic.php';
$config=dirname(__FILE__).'/config/'.APPLICATION_ENV.'.console.php';

//Эту часть добавил из $yiic
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

if(APPLICATION_ENV != 'production'){
	//YII DEBUG on
	defined('YII_DEBUG') or define('YII_DEBUG', true);
}


$yii=dirname(__FILE__).'/../../yiiframework/yii.php';
require_once($yii);

if(isset($config))
{
	$app=Yii::createConsoleApplication($config);
	$app->commandRunner->addCommands(YII_PATH.'/cli/commands');
}
else
	$app=Yii::createConsoleApplication(array('basePath'=>dirname(__FILE__).'/cli'));

$env=@getenv('YII_CONSOLE_COMMANDS');
if(!empty($env))
	$app->commandRunner->addCommands($env);

//эту переменную тоже определил я
Yii::setPathOfAlias('webroot', dirname(dirname(__FILE__)) );

$app->run();

?>
