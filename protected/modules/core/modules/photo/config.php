<?php
return array(
	'modules' => array(),
	'import' => array(
		'core.modules.photo.models.*', 
	),
	'params' => array(
		'watermark_image' => 'themes/backend/assets/images/watermarks/wm.png',
		'watermark_image_dark' => 'themes/backend/assets/images/watermarks/wm_dark.png',
		'watermark_image_light' => 'themes/backend/assets/images/watermarks/wm_light.png',

		'images'=>array(
			'photoalbumThumb' => array(
				'path' => 'storage/images/photoalbum/thumb/',
				'placeholder' => 'img/placeholders/photoalbum/',
				'sizes' => array(
					'original' => array(),
					'medium' => array('width'=>200, 'height'=>120, 'crop'=>true),
					'thumb' => array('width'=>190, 'height'=>190, 'crop'=>true),
			    )
		    ),
		    'photoalbumPhoto' => array(
				'path' => 'storage/images/photoalbum/photos/',
				'placeholder' => 'img/placeholders/photo/',
				'sizes' => array(	
					'original' => array(),
					'thumb' => array('width'=>120, 'height'=>120, 'crop'=>true),
					'big' => array('width'=>800, 'height'=>800, /*'watermark' => true*/),
			    )
		    ),
		),
		
	),
	'components' => array(),
	'rules' => array(
		 //other rules in backend
        ADMIN_PATH.'/photo' => 'core/photo/default/album/index',
        ADMIN_PATH.'/photo/<controller:(album|default)>/<action:\w+>/<id:\d+>' => 'core/photo/<controller>/<action>',
        ADMIN_PATH.'/photo/<controller:(album|default)>/<action:\w+>' => 'core/photo/<controller>/<action>',

        ADMIN_PATH.'/photo/<action:\w+>/<id:\d+>' => 'core/photo/default/back/<action>',
        ADMIN_PATH.'/photo/<action:\w+>' => 'core/photo/default/back/<action>',

	),
);