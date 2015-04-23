<?php

$files = glob(dirname(dirname(__FILE__)).'/modules/*/config.php');
$modulePaths = array();

foreach($files as $file){
    $name = preg_replace('#^.*/modules/([^\.]*)/config\.php$#', '$1', $file);
    $config = require($file);
    
    if(!isset($config['disabled']) || $config['disabled'] !== false){
        $modulePaths[$name] = "application.modules.{$name}.migrations";
    }
}


return CMap::mergeArray(
    // наследуемся от main.php
    require(dirname(__FILE__).'/main.php'),

    array(
        'components'=>array(

            'cache'=>array('class'=>'CFileCache'),

            'db'=> array( 
                // настройка соединения с базой
                'connectionString' => 'mysql:host=mysql.prod;dbname=hends',
                'username' => 'hendsuser',
                'password' => 'I63USoG7',
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

            'request' => array( //это нужно для корректного создания url, из консоли
                'hostInfo' => 'http://new.marketrf.ru',
                'scriptUrl' => '',
            ),

            'amqp' => array(
                'class' => 'core.components.amqp.SAMQP',
                'host' => 'rabbitmq.srv',
                'port' => '5672',
                'login'=>'market',
                'password'=>'xbbGhbUK5RmqB9qdHbmi',
                'vhost'=>'/',
            ),

            /*'elasticSearch' => array(
                'class' => 'YiiElasticSearch\Connection',
                'baseUrl' => 'http://localhost:9200/',
            ),*/
           
        ),

        'commandMap' => array(
            
            'migrate' => array(
                // alias of the path where you extracted the zip file
                'class' => 'core.components.migrate.EMigrateCommand',
                // this is the path where you want your core application migrations to be created
                'migrationPath' => 'application.db.migrations',
                // the name of the table created in your database to save versioning information
                'migrationTable' => 'tbl_migration',
                // the application migrations are in a pseudo-module called "core" by default
                'applicationModuleName' => 'root',
                // define all available modules
                // 'modulePaths' => $modulePaths,
                // // here you can configrue which modules should be active, you can disable a module by adding its name to this array
                // 'disabledModules' => array(
                //     'admin', 'anOtherModule', // ...
                // ),
                // // the name of the application component that should be used to connect to the database
                // 'connectionID'=>'db',
                // // alias of the template file used to create new migrations
                // 'templateFile'=>'application.db.migration_template',
            ),
        ),

        'params' => array(
            'upload_max_filesize' => '512M',

            'nodejsUrl' => 'http://new.marketrf.ru:3000',

            'settings' => array(
                'adminEmail'=> 'alikmanukian@gmail.com',
                'notifyEmail' => 'noreply@marketrf.ru',
                'infoEmail' => 'info@marketrf.ru',
            )
        )

        
    )
);
