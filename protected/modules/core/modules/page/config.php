<?php
return array(
    'modules' => array(),
    'import' => array(
    	'core.modules.page.models.*', 
    ),
    'params' => array(),
    'components' => array(),
    'rules' => array(
    	 //other rules in backend
        ADMIN_PATH.'/page' => 'core/page/back/index',
        ADMIN_PATH.'/page/<action:\w+>/<id:\d+>' => 'core/page/back/<action>',
        ADMIN_PATH.'/page/<action:\w+>' => 'core/page/back/<action>',

        ADMIN_PATH.'/block' => 'core/page/blocks/index',
        ADMIN_PATH.'/block/<action:\w+>/<id:\d+>' => 'core/page/blocks/<action>',
        ADMIN_PATH.'/block/<action:\w+>' => 'core/page/blocks/<action>',

        // '<route:[a-zA-Z0-9\-\_]+>' => 'site/page/index',
    ),
);