<?php


// uncomment the following to define a path alias
Yii::setPathOfAlias('app', 'application');
Yii::setPathOfAlias('modules', 'application.modules');
Yii::setPathOfAlias('components', 'application.components');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

defined('BASEPATH') or define('BASEPATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..');

require(dirname(__FILE__).'/modules.php');

return array(
	'basePath'=> BASEPATH,
	'name'=>'«Hay Craft»',
	'charset'=>'utf-8',
	// 'sourceLanguage'=>'ru',
	'language'=>'ru',

	// preloading 'log' component
	'preload'=>array('log'),

	'aliases' => array(

		'app'        => 'application',
		'ext'        => 'app.extensions',
		'components' => 'app.components',
		'behaviors'  => 'components.behaviors',
		'modules'	 => 'app.modules',
		'core'	     => 'modules.core',

		// yiistrap configuration
		'bootstrap'  => 'core.extensions.bootstrap', // change if necessary
		// yiiwheels configuration
		'yiiwheels'  => 'core.extensions.yiiwheels', // change if necessary
		'hends'		 => 'app.modules.hends',
		'banner'	 => 'app.modules.banner',
	),

	// autoloading model and component classes
	'import'=> CMap::mergeArray( array(
		'app.models.*',
		'app.extensions.*',
		'app.components.*',
		'components.behaviors.*',
		'components.behaviors.taggable.*',
		'core.controllers.*',
		'core.behaviors.*',
		'core.components.*',
		'core.helpers.*',
		'core.widgets.common.*',
		'core.widgets.ui.*',
		'ext.shoppingCart.*',
		'app.widgets.*',
		'app.widgets.bannerslider.*',

	), $config['import']),

	'modules'=>CMap::mergeArray( array(

	), $config['modules']),

	// application components
	'components'=> CMap::mergeArray( array(

		'db' => array(
			'charset' => 'utf8',
			'tablePrefix' => '',
			'emulatePrepare' => true,
			'schemaCachingDuration' => 3600,
		),

		'urlManager'=> array(
			'class'=>'core.components.urlManager.LangUrlManager',
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'caseSensitive'=>false,

			'rules'=>CMap::mergeArray($config['rules'], array(
				'/' => 'site/index',

				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<module:\w+>/<controller:\w+>/<action:\w+>/*' => '<module>/<controller>/<action>',
				'<module:\w+>/<submodule:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<submodule>/<controller>/<action>',
				'<module:\w+>/<submodule:\w+>/<controller:\w+>/<action:\w+>/*' => '<module>/<submodule>/<controller>/<action>',

				'<controller:\w+>/<action:\w+>/*' => '<controller>/<action>',
			) ),
		),

		'mailer' => array(
			'class' => 'core.components.mailer.EMailer',
			'pathViews' => 'app.views.email',
			'pathLayouts' => 'app.views.email.layouts'
		),

		'mail' => array(
			'class' => 'core.components.SendMail',
		),

		'sms' => array(
			'class' => 'core.components.sms.ESms',
			'provider' => [
				'class' => 'core.components.sms.providers.Sms2Provider',
				'login' => '',
				'password' => ''
			]
		),

		'shoppingCart' => [
			'class' => 'ext.shoppingCart.EShoppingCart',
			'model' => 'ShoppingCart'
		],

		'file' => array(
			'class' => 'core.components.CFile',
		),

		'assetManager' => array(
			'class' => 'modules.core.components.SAssetManager'
		),

		'clientScript' => array(
			'coreScriptPosition' => CClientScript::POS_END,

			'class' => 'core.extensions.yii-EClientScript.SClientScript',
			'combineScriptFiles' => false, // By default this is set to true, set this to true if you'd like to combine the script files
			'combineCssFiles' => false, // By default this is set to true, set this to true if you'd like to combine the css files
			'optimizeScriptFiles' => false, // @since: 1.1
			'optimizeCssFiles' => false, // @since: 1.1
			'optimizeInlineScript' => false, // @since: 1.6, This may case response slower
			'optimizeInlineCss' => false, // @since: 1.6, This may case response slower

			'skipScripts' => array('//www.google.com/jsapi'),

			'packages' => array(
				'underscore' => array( //JS template library
					'basePath' => 'core.assets.js.libraries',
					'js'=>array(YII_DEBUG ? 'underscore/underscore.js' : 'underscore/underscore-min.js'),
				),

				'mustache' => array( //JS template library
					'basePath' => 'core.assets.js.libraries',
					'js'=>array('mustache/mustache.js'),
				),

				'backbone' => array( //JS MVC FRAMEWORK backbone
					'basePath' => 'core.assets.js.libraries',
					'js'=>array(YII_DEBUG ? 'backbone/backbone.js' : 'backbone/backbone-min.js'),
					'depends' => array('jquery', 'underscore')
				),

				'selectstyler-backend' => array(
					'basePath' => 'core.assets.js.plugins',
					'js' => array('select-styler/js/select.js', 'select-styler/js/jquery.actual.min.js'),
					'css' => array('select-styler/stylesheets/select-backend.css'),
					'depends' => array('jquery')
				),

				'selectstyler-frontend' => array(
					'basePath' => 'core.assets.js.plugins',
					'js' => array('select-styler/js/select.js', 'select-styler/js/jquery.actual.min.js'),
					'css' => array('select-styler/stylesheets/select-frontend.css'),
					'depends' => array('jquery')
				),

				'jalerts' => array(
					'basePath' => 'core.assets.js.plugins',
					'js' => array('jquery-alerts/jquery.alerts.js'),
					'css' => array('jquery-alerts/jquery.alerts.css'),
					'depends' => array('jquery')
				),

				'messenger' => array(
					'basePath' => 'app.assets.plugins',
					'js' => array('jquery-notifications/js/messenger.min.js'),
					'css' => array('jquery-notifications/css/messenger.css', 'jquery-notifications/css/messenger-theme-flat.css'),
					'depends' => array('jquery')
				),

				'bootstrap' => array(
					'basePath' => 'app.assets.plugins.bootstrapv3',
					'js' => array(
						'js/'.(YII_DEBUG ? 'bootstrap.js' : 'bootstrap.min.js')),
					'css' => array(
						'css/bootstrap.min.css',
						'css/'.(YII_DEBUG ? 'bootstrap-theme.css' : 'bootstrap-theme.min.css')),
					'depends' => array('jquery')
				),

				'backend-globals' => array(
					'basePath' => 'core.themes.webarch.assets',
					'js' => array('js/core.js', 'js/custom/events.js'),
					'css' => array(
						'css/animate.min.css',
						'css/style.css',
						'css/responsive.css',
						'css/custom-icon-set.css',
						'css/custom-styles.css',
						'css/helpers.css',
					),
					'depends' => array('jquery')
				),

				'backend-globals' => array(
					'basePath' => 'core.themes.webarch.assets',
					'js' => array('js/core.js', 'js/custom/events.js'),
					'css' => array(
						'css/animate.min.css',
						'css/style.css',
						'css/responsive.css',
						'css/custom-icon-set.css',
						'css/custom-styles.css',
						'css/helpers.css',
					),
					'depends' => array('jquery')
				),
			)
		),

		'curl' => array(
			'class' => 'core.components.curl.Curl'
		),

		'format' => array(
			'class' => 'core.components.SFormatter'
		),

		'settings' => array(
			'class' => 'core.components.SystemSettings'
		),

		/*'currency'=> array(
			'class'=>'store.components.SCurrencyManager'
		),*/

		'fmenu' => array(
			'class' => 'core.widgets.ui.SFrontMenu'
		),

		'bmenu' => array(
			'class' => 'core.widgets.ui.SBackMenu'
		),

		'viewRenderer'=>array(
			'class'=>'core.components.smarty-renderer.ESmartyViewRenderer',
			'fileExtension' => '.tpl',
			'pluginsDir' => 'core.vendors.Smarty.plugins',
			'smartyDir' => 'core.vendors.Smarty'
			//'configDir' => 'application.smartyConfig',
			//'prefilters' => array(array('MyClass','filterMethod')),
			//'postfilters' => array(),
			//'config'=>array(
			//    'force_compile' => YII_DEBUG,
			//   ... any Smarty object parameter
			//)
		),

		'ePdf' => [
			'class' => 'core.components.yii-pdf.EYiiPdf',
			'params' => [
				'mpdf' => [
					'librarySourcePath' => 'core.vendors.mpdf.*',
					'constants' => [
						'_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
					],
					'class'=>'mpdf',
				],
				'HTML2PDF' => [
					'librarySourcePath' => 'core.vendors.html2pdf.*',
					'classFile'         => 'html2pdf.class.php', // For adding to
				]
			]
		 ],

		// yiiwheels configuration
		'yiiwheels' => array(
			'class' => 'yiiwheels.YiiWheels',
		),

		// yiistrap configuration
		'bootstrap' => array(
			'class' => 'core.components.Bootstrap',
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'/site/error',
		),

		'request' => array(
			'class' => 'HttpRequest',
			'enableCsrfValidation' => true
		),

		'response' => array(
			'class' => 'HttpResponse'
		),

		/*'foursquare' => array(
			'class' => 'core.components.foursquare.FoursquareComponent',
			'clientId' => 'N3QTRSBRL41JHOPTJMU4QOIBUOTH4MMAAFQ0OLUO1W3M3Q4B',
			'clientSecret' => '1NJTEEMMYTK5S1YVQLFAFWOU3RSWQILC43533MEEYN3TOQLW',
		),*/

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
				array(
					'logFile'=> 'warning.log',
					'class'  => 'CFileLogRoute',
					'levels' => 'pricelist', // error, warning
				),
				array(
					'class'=>'ext.yii-sentry.components.RSentryLogRoute',
					'levels'=>'error, warning',
				)
			),
		),
	), $config['components']),

	'behaviors' => array(
		'ApplicationConfigBehavior', //это строка должна быть первой
		// 'core.components.urlManager.LanguageBehavior',
	),

	'params' => CMap::mergeArray( array(

		'languages'=>array('ru'=>'Русский', 'hy'=>'Հայերեն', 'en'=>'Eglish'),
		'defaultLanguage'=>'ru',

		'upload_size_limit' => 20 * 1024 * 1024,
		'upload_max_filesize' => '128M',
		'upload_allowed_extensions' => array("jpg", "jpeg", "png", "gif"),

		'storage' => 'storage',
		'upload_tmp_folder' => 'storage/.tmp/',

		'email_templates' => '//email_templates/',
		'locales' => array('ru'=>'ru_RU'/*, 'en'=>'en_GB', 'hy'=>'hy_AM'*/),

		'cache_duration' => 7 * 24 * 60 * 60, //одна неделя

		'home' => '/site/index',

		'adminHome' => '/admin/back/index',

		'nodejsUrl' => YII_DEBUG ? 'http://market.dev:3000' : 'http://new.marketrf.ru:3000',

		'settings' => array(
			'adminEmail'	=>	'admin@haycraft.am',
			'notifyEmail'	=>	'Маркет.рф <noreply@haycraft.am>',
			'infoEmail'		=>	'Маркет.рф <info@haycraft.am>',
		)
	), $config['params']),
);
?>
