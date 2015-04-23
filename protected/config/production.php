<?php


return CMap::mergeArray(
    // наследуемся от main.php
    require(dirname(__FILE__).'/main.php'),

    array(
        'theme' => 'megatron',
        
        'preload' => array(
            'debug'
        ),

        'catchAllRequest' => file_exists(dirname(__FILE__)."/.maintenance") && 
                        !(isset($_COOKIE['secret']) && $_COOKIE['secret']=="password") ? 
                        array('maintenance/index') : null,

        'components'=>array(

            // кеширование
            'cache'=>array('class'=>'CApcCache'),

            // кеширование сессий
            'sessionCache' => array('class' => 'CApcCache'),

            'session' => array(
                'class' =>'CCacheHttpSession',
                'cacheID' => 'sessionCache',
                'timeout' => 3600 * 24 // 1 день
            ),

            // дебагер
            'debug' => array(
                'class' => 'core.extensions.yii2-debug.Yii2Debug',
                'enabled' => true, //TODO: after finish delete it
                'allowedIPs' => array('82.199.193.118')
            ),

            'db2'=> array( 
                // настройка соединения с базой
                'class'=>'system.db.CDbConnection',
                'connectionString' => 'mysql:host=mysql.prod;dbname=market',
                'username' => 'alexander',
                'password' => 'HflbjfrnbdysqGjvbljh123',
                'charset' => 'utf8',
                // включаем профайлер
                'enableProfiling'=>true,
                // показываем значения параметров
                'enableParamLogging' =>true,
            ), 

            'db'=> array( 
                // настройка соединения с базой
                'connectionString' => 'mysql:host=mysql.prod;dbname=marketrf_new',
                'username' => 'alexander',
                'password' => 'HflbjfrnbdysqGjvbljh123',

                'enableProfiling' => true, //TODO: after finish delete it
                'enableParamLogging' => true, //TODO: after finish delete it
            ),
           
            'clientScript' =>array(
                
                'scriptMap'=>array( //TODO: может это убрать ? 
                    'jquery.js'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js', 
                    'jquery.min.js'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'
                ),
            ),

            'facebook'=>array(
                'class' => 'core.extensions.facebook.SFacebook',
                'appId'=>'158066407721898', // needed for JS SDK, Social Plugins and PHP SDK
                'secret'=>'061d308f3d67b5f3285131d503e780b0', // needed for the PHP SDK
            ),

            
            'mail' => array(
                "host" => "mail.city-mobil.ru", //smtp сервер
                "debug" => 0, //отображение информации дебаггера (0 - нет вообще)
                "auth" => true, //сервер требует авторизации
                "port" => 25, //порт (по-умолчанию - 25)
                "secure" => "tls",
                "username" => "admin@marketrf.ru", //имя пользователя на сервере
                "password" => "vTtQqe1R", //пароль
                "fromname" => "", //имя
                "from" => "noreply@marketrf.ru", //от кого
                "charset" => "utf-8",
                "layout" => "main"
            ),
            
            'messages'=>array(
                'onMissingTranslation' => array('MissingMessages', 'toTable')
            ),

            'to1c' => array(
                'class' => 'market.components.To1C',
                'login' => 'ws',
                'password' => 'ws90456'
            )
        ),

        'behaviors' => array( 
            'ApplicationConfigBehavior', //это строка должна быть первой
            'core.components.urlManager.LanguageBehavior',
        ),

        'params' => array(
            'settings' => array(
                'adminEmail'=>'alikmanukian@gmail.com',
                'notifyEmail' => 'noreply@marketrf.ru',
                'infoEmail' => 'info@marketrf.ru',
            )
        )
        
    )
);