<?


/**
 * Иногда возникает потребность работать с данными из приложения Yii в стороннем скрипте, например, вывести количество записей в таблице или произвести иные действия. Удобней всего делать это, используя классы Yii.
 *
 * После этого можно в любом скрипте подключить файл yiiapp.php и получить готовое приложение со всеми его возможностями:
 */

date_default_timezone_set('Europe/Moscow');

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yiiframework/yii.php';
$helpers = dirname(__FILE__).'/protected/modules/core/helpers/Globals.php';

defined('APPLICATION_ENV') 
 	|| define('APPLICATION_ENV', 
 			(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('USER_ENV') 
 	|| define('USER_ENV', 
 			(getenv('USER_ENV') ? getenv('USER_ENV') : 'default'));

defined('ADMIN_PATH') 
 	|| define('ADMIN_PATH', 
 			(getenv('ADMIN_PATH') ? getenv('ADMIN_PATH') : 'admin'));


if(APPLICATION_ENV != 'production'){
	//YII DEBUG on
	defined('YII_DEBUG') or define('YII_DEBUG', true);
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
}


$config = dirname(__FILE__).'/protected/config/'.APPLICATION_ENV.'.php';

require_once($yii);
require_once($helpers);

Yii::createWebApplication($config);



?>