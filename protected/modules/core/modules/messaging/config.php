<?php
return array(
    'modules' => array(),
    'import' => array(
    	'core.modules.messaging.models.*',
        'core.modules.messaging.extensions.*',
        'core.modules.messaging.components.*'
    ),
    'params' => array(),
    'components' => array(),
    'rules' => array(
        ADMIN_PATH.'/messaging/<controller:(templates|logs)>' => 'core/messaging/<controller>/index',
        ADMIN_PATH.'/messaging/<controller:(templates|logs)>/<action:\w+>/<id:\d+>' => 'core/messaging/<controller>/<action>',
        ADMIN_PATH.'/messaging/<controller:(templates|logs)>/<action:\w+>' => 'core/messaging/<controller>/<action>',
    	

    ),
);