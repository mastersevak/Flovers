<?php
return array(
	'modules' => array(),
	'import' => array(
		//user module
		'core.modules.user.models.*',
		'core.modules.user.components.*',
        'core.modules.user.behaviors.*',
	),
    'params' => array(
        'images'=>array(
            'user' => array(
                'path' => 'storage/images/user/photos',
                'placeholder' => 'storage/placeholders/user',
                'sizes'=>array(
                    'original' => array(),
                    'big' => array('width'=>140, 'height'=>140, 'crop'=>true),
                    'thumb' => array('width'=>50, 'height'=>50, 'crop'=>true),
                )
            )
        ),
    ),
	'components' => array(
        'user' => array(
            'class' => 'WebUser',
			'allowAutoLogin'=>true,
			'loginUrl'=>array('/core/user/back/login'),
        )
    ),
    'rules' => array(
    	//login, logout, registration for admin
        ADMIN_PATH.'/<action:(login|logout|registration|ajaxlogin|lock|unlock)>' => 'core/user/back/<action>', 

        ADMIN_PATH.'/user/<type:blocked>' => 'core/user/back/index',
        ADMIN_PATH.'/user' => 'core/user/back/index',
     
        ADMIN_PATH.'/user/<action:\w+>/<id:\d+>' => 'core/user/back/<action>',
        ADMIN_PATH.'/user/<action:\w+>' => 'core/user/back/<action>',

        //login, logout - frontend
        '<action:(login|logout|registration|forgotpassword|resetpassword|changepassword|profile)>' => 'auth/<action>',
        //activate user
        'activate/<username:\w+>/<key:\w+>' => 'auth/activate', 
    )
);