<?php
return array(
	'modules' => array(),
	'import' => array(
		'application.modules.admin.models.*', 
	),
	'params' => array(
	),
	'components' => array(),
	'rules' => array(
		//default admin page
		ADMIN_PATH => 'admin/back/index',
		ADMIN_PATH.'/clearcache' => 'admin/back/clearcache',
		ADMIN_PATH.'/<controller:(backup|settings|lookup|notificationtemplate)>' => 'admin/<controller>/index',
		ADMIN_PATH.'/<controller:(backup|settings|lookup|notificationtemplate)>/<action:\w+>/<id:\d+>' => 'admin/<controller>/<action>',
        ADMIN_PATH.'/<controller:(backup|settings|lookup|notificationtemplate)>/<action:\w+>' => 'admin/<controller>/<action>',
	)
);