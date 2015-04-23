<?php

return array( 
	'modules' => array(),
	'import' => array(
		'application.modules.core.models.*', 
		'application.modules.core.behaviors.*',
		'application.modules.core.widgets.*',
		'application.modules.core.widgets.avatar.*',
		'application.modules.core.widgets.doaction.*',
		'application.modules.core.widgets.uploader.*',
		'application.modules.core.widgets.jcrop.*',
		'application.modules.core.widgets.comments.*',
		'application.modules.core.validators.*'
	),
	'params' => array(
	),
	'components' => array(
		'clientScript' => array(
			'packages' => array(
				'comments' => array(
					'basePath' => 'application.modules.core.widgets.comments.assets',
					'js' => array('js/comments.js'),
					'css' => array('css/comments.css'),
					// Зависимость от другого пакета
            		'depends'=>array('jquery', 'mustache'),
				),
			),
		),
	),
	'rules' => array(
		'clearcache' => '/core/base/clearcache',
		'clearassets' => '/core/base/clearassets',
		'phpinfo' => '/core/base/phpinfo'
	)
);
