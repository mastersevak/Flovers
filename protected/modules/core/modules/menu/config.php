<?php
return array(
	'modules' => array(),
	'import' => array(
		//user module
		'core.modules.menu.models.*',
	),
    'params' => array(
    ),
	'components' => array(
    ),
    'rules' => array(

        ADMIN_PATH.'/menu' => 'core/menu/back/index',
    	
        ADMIN_PATH.'/menu/<controller:(back)>' => 'core/menu/<controller>/index',
        ADMIN_PATH.'/menu/<controller:(back)>/<action:\w+>/<id:\d+>' => 'core/menu/<controller>/<action>',
        ADMIN_PATH.'/menu/<controller:(back)>/<action:\w+>' => 'core/menu/<controller>/<action>',
        
        ADMIN_PATH.'/menu/<action:\w+>/<id:\d+>' => 'core/menu/back/<action>',
        ADMIN_PATH.'/menu/<action:\w+>' => 'core/menu/back/<action>',

    )
);