<?php
return array(
	'modules' => array(),
	'import' => array(
		//модуль для исполъзования openid, oauth
		'core.modules.social.extensions.eoauth.*',
        'core.modules.social.extensions.eoauth.lib.*',
        'core.modules.social.extensions.lightopenid.*',
        'core.modules.social.extensions.eauth.*',
        'core.modules.social.extensions.eauth.services.*',
        'core.modules.social.extensions.eauth.custom_services.*',

        'core.modules.social.models.*'
	),
	'params' => array(),
	'components' => array(
		'loid' => array(
            'class' => 'core.modules.social.extensions.lightopenid.loid',
        ),
        'eauth' => array(
			'class' => 'core.modules.social.extensions.eauth.EAuth',
			'popup' => true, // Use the popup window instead of redirecting.
			'cache' => false, // Cache component name or false to disable cache. Defaults to 'cache'.
			'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
			'services' => array( // You can change the providers and their classes.
			    'google_oauth' => array(
			        // register your app here: https://code.google.com/apis/console/
			        // redirect uri: http://mega-real.ru/login?service=google_oauth
			        'class' => 'CustomGoogleOAuthService',
			        'client_id' => '844888079479.apps.googleusercontent.com',
			        'client_secret' => 'cYrjkjA2CinHfwC8I-JrvV4f',
			        'title' => 'Google (OAuth)',
			    ),
			    'yandex_oauth' => array(
			        // register your app here: https://oauth.yandex.ru/client/my
			        // redirect uri: http://mega-real.ru/login?service=yandex_oauth
			        'class' => 'CustomYandexOAuthService',
			        'client_id' => 'ccbccf854d9347879673feda491aacb8',
			        'client_secret' => '78c58314bbf1420380909e998db46dae',
			        'title' => 'Yandex (OAuth)',
			    ),
			    'facebook' => array(
			        // register your app here: https://developers.facebook.com/apps/
			        // alikmanukian@gmail.com (надо поменять потом)
			        'class' => 'CustomFacebookService',
			        'client_id' => '158066407721898', 
			        'client_secret' => '061d308f3d67b5f3285131d503e780b0',
			        'scope' => 'email', 
			    ),
			    'mailru' => array(
			        // register your app here: http://api.mail.ru/sites/my/add
			        'class' => 'CustomMailruService',
			        'client_id' => '712041',
			        'client_secret' => '9aee060cb66ec77c234d5145d46d8523' //amanukian@mail.ru (надо поменять потом)
			    ), 
			    // 'odnoklassniki' => array(
			    //     // register your app here: http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=13992188
			    //     // ... or here: http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev
			    //     'class' => 'CustomOdnoklassnikiService',
			    //     'client_id' => '...',
			    //     'client_public' => '...',
			    //     'client_secret' => '...',
			    //     'title' => 'Odnokl.',
			    // ),

			    // 'google' => array(
			    //     'class' => 'CustomGoogleService', //open id
			    // ),
			    // 'yandex' => array(
			    //     'class' => 'CustomYandexOAuthService', //open id
			    // ),

			    // 'twitter' => array(
			    //     // register your app here: https://dev.twitter.com/apps/new
			    //     'class' => 'TwitterOAuthService',
			    //     'key' => '...',
			    //     'secret' => '...',
			    // ),
			    // 'linkedin' => array(
			    //     // register your app here: https://www.linkedin.com/secure/developer
			    //     'class' => 'LinkedinOAuthService',
			    //     'key' => '...',
			    //     'secret' => '...',
			    // ),
			    // 'github' => array(
			    //     // register your app here: https://github.com/settings/applications
			    //     'class' => 'GitHubOAuthService',
			    //     'client_id' => '...',
			    //     'client_secret' => '...',
			    // ),
			    // 'live' => array(
			    //     // register your app here: https://manage.dev.live.com/Applications/Index
			    //     'class' => 'LiveOAuthService',
			    //     'client_id' => '...',
			    //     'client_secret' => '...',
			    // ),  
			    // 'vkontakte' => array( //возникает проблема с долгим таймаутом и сайт виснет
			    //     // register your app here: https://vk.com/editapp?act=create&site=1
			    //     'class' => 'CustomVKontakteService',
			    //     'client_id' => '3465106',
			    //     'client_secret' => '9vKJyYKR2azh5jMdD35e', //alikmanukian@gmail.com (надо поменять потом)
			    // ),
			    // 'moikrug' => array(
			    //     // register your app here: https://oauth.yandex.ru/client/my
			    //     'class' => 'MoikrugOAuthService',
			    //     'client_id' => '...',
			    //     'client_secret' => '...',
			    // ),
			), //services
		) //eauth
	),
	'rules' => array()
);