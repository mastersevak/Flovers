<?php
return array(
	'modules' => array(),
	'import' => array(
		'core.modules.post.models.*', 
	),
	'params' => array(),
	'components' => array(),
	'rules' => array(
        ADMIN_PATH.'/post/<controller:video>' => 'core/post/<controller>/index',
        ADMIN_PATH.'/post/<controller:video>/<action:\w+>' => 'core/post/<controller>/<action>',
        ADMIN_PATH.'/post/<controller:video>/<action:\w+>/<id:\d+>' => 'core/post/<controller>/<action>',

	),
);