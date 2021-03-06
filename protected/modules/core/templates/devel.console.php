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

            'db2' => array( 
                // настройка соединения с базой
                'class'=>'system.db.CDbConnection',
                'connectionString' => 'mysql:host=localhost;dbname=market;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
            ),

            'db' => array( 
                // настройка соединения с базой
                'connectionString' => 'mysql:host=localhost;dbname=marketrf_new;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock',
                'username' => 'root',
                'password' => '',
            ),
           
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
        )
        
    )
);