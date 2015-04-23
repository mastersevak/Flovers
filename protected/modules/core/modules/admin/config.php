<?php
return array(
	'modules' => array(),
	'import' => array(
		'core.modules.admin.models.*', 
		'core.modules.admin.models.notifies.*', 
	),
	'params' => array(
	),
	'components' => array(),
	'rules' => array(
		//default admin page
		ADMIN_PATH => 'core/admin/back/index',
		ADMIN_PATH.'/clearcache' => 'core/admin/back/clearcache',
		ADMIN_PATH.'/<controller:(back|backup|settings|lookup|maintenance|settings)>' => 'core/admin/<controller>/index',
		ADMIN_PATH.'/<controller:(back|backup|settings|lookup|maintenance|settings)>/<action:\w+>/<id:\d+>' => 'core/admin/<controller>/<action>',
        ADMIN_PATH.'/<controller:(back|backup|settings|lookup|maintenance|settings)>/<action:\w+>' => 'core/admin/<controller>/<action>',
	)
);