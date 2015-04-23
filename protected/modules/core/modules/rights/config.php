<?php
return array(
	'modules' => array(),
	'import' => array(
		'core.modules.rights.*',
		'core.modules.rights.components.*',
		'core.modules.rights.components.behaviors.*',
		'core.modules.rights.components.dataproviders.*',
		'core.modules.rights.controllers.*',
		'core.modules.rights.models.*',
	),
    'params' => array(),
	'components' => array(
        'authManager'=>array(
            'class'=>'RDbAuthManager',
            'connectionID'=>'db',
        )
    ),
    'rules' => array(
    	//login, logout, registration for admin
        ADMIN_PATH.'/rights' => 'core/rights/assignment/view', 
        ADMIN_PATH.'/rights/<controller:\w+>/<id:\d+>' => 'core/rights/<controller>/view',
		ADMIN_PATH.'/rights/<controller:\w+>/<action:\w+>/<id:\d+>' => 'core/rights/<controller>/<action>',
		ADMIN_PATH.'/rights/<controller:\w+>/<action:\w+>' => 'core/rights/<controller>/<action>',
        
    )
);
				