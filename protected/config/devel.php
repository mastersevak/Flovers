<?php

return CMap::mergeArray(
	// наследуемся от main.php
	require(dirname(__FILE__).'/main.php'), [
		'preload' => ['debug'],

		'components'=>array(

			// кеширование
			'cache' => ['class' => 'CFileCache'],

			// // кеширование сессий
			'sessionCache' => ['class' => 'CFileCache'],

			'session' => [
				'class'		=>	'CCacheHttpSession',
				'cacheID'	=>	'sessionCache',
				'timeout'	=>	3600 * 24 // 1 день
			],

			//дебагер
			'debug' => ['class' => 'core.extensions.yii2-debug.Yii2Debug'],

			'db' => [
				// настройка соединения с базой
				'connectionString' => 'mysql:host=localhost;dbname=flowers',
				'username' => 'root',
				'password' => '',
				// включаем профайлер
				'enableProfiling' => true,
				// показываем значения параметров
				'enableParamLogging' => true,
			],

			'eauth' => [
				'services' => [
					'facebook' => [
						'class'			=> 'CustomFacebookService',
						'client_id'		=> '443241905775604',
						'client_secret'	=> 'bba1c81fab12562a4e223c38e1b5001e',
						'scope'			=> 'email',
					]
				]
			],

			'facebook'	=> [
				'class'		=>	'core.extensions.facebook.SFacebook',
				'appId'		=>	'443241905775604', // needed for JS SDK, Social Plugins and PHP SDK
				'secret'	=>	'bba1c81fab12562a4e223c38e1b5001e', // needed for the PHP SDK
				//'fileUpload'=>false, // needed to support API POST requests which send files
				//'trustForwarded'=>false, // trust HTTP_X_FORWARDED_* headers ?
				//'locale'=>'en_US', // override locale setting (defaults to en_US)
				//'jsSdk'=>true, // don't include JS SDK
				//'async'=>true, // load JS SDK asynchronously
				//'jsCallback'=>false, // declare if you are going to be inserting any JS callbacks to the async JS SDK loader
				//'status'=>true, // JS SDK - check login status
				//'cookie'=>true, // JS SDK - enable cookies to allow the server to access the session
				//'oauth'=>true,  // JS SDK - enable OAuth 2.0
				//'xfbml'=>true,  // JS SDK - parse XFBML / html5 Social Plugins
				//'frictionlessRequests'=>true, // JS SDK - enable frictionless requests for request dialogs
				//'html5'=>true,  // use html5 Social Plugins instead of XFBML
				//'ogTags'=>array(  // set default OG tags
					//'title'=>'MY_WEBSITE_NAME',
					//'description'=>'MY_WEBSITE_DESCRIPTION',
					//'image'=>'URL_TO_WEBSITE_LOGO',
				//),
			],

			'mail' => [
				"host"		=> "mail.city-mobil.ru", //smtp сервер
				"debug"		=> 0, //отображение информации дебаггера (0 - нет вообще)
				"auth"		=> true, //сервер требует авторизации
				"port"		=> 25, //порт (по-умолчанию - 25)
				"secure"	=> "tls",
				"username"	=> "admin@marketrf.ru", //имя пользователя на сервере
				"password"	=> "vTtQqe1R", //пароль
				"fromname"	=> "", //имя
				"from"		=> "noreply@marketrf.ru", //от кого
				"charset"	=> "utf-8",
				"layout"	=> "main"
			],

			'messages' => [
				'onMissingTranslation' => ['MissingMessages', 'toTable']
			],

			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					array(
						'logFile'=> 'info.log',
						'class'  => 'CFileLogRoute',
						'levels' => 'info', // error, warning
					),
					array(
						'logFile'=> 'error.log',
						'class'  => 'CFileLogRoute',
						'levels' => 'error', // error, warning
					),
					array(
						'logFile'=> 'warning.log',
						'class'  => 'CFileLogRoute',
						'levels' => 'warning', // error, warning
					),
				),
			),

			'csv' => array(
				'class' => 'modules.market.components.CsvReader'
			),

			'to1c' => array(
				'class'		=> 'market.components.To1C',
				'login'		=> 'ws',
				'password'	=> 'ws90456'
			),

			'amqp' => [
				'class'		=>	'core.components.amqp.SAMQP',
				'host'		=>	'localhost',
				'port'		=>	'5672',
				'login'		=>	'amanukian',
				'password'	=>	'MYMdeq1Tv',
				'vhost'		=>	'/',
			],
		),

		'behaviors' => array(
			'ApplicationConfigBehavior', //это строка должна быть первой
			'core.components.urlManager.LanguageBehavior',
		),


		'params' => array(

			'nodejsUrl' => 'http://market.local:3000',

			'settings' => array(
				'adminEmail'=>'alikmanukian@gmail.com',
				'notifyEmail' => 'Маркет.рф <noreply@marketrf.ru>',
				'infoEmail' => 'Маркет.рф <info@marketrf.ru>',
			)
		)
	]
);
