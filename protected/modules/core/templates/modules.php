<?php 


$config = array(
	'modules'      => array(),
	'import'       => array(),
	'params'       => array(),
	'components'   => array(),
	'rules'        => array(),
);

// Получаем настройки модулей
// $files = glob(dirname(__FILE__) . '/modules/*.php');
$files = glob(dirname(dirname(__FILE__)).'/modules/*/config.php');
$submodulesConfigs = glob(dirname(dirname(__FILE__)).'/modules/*/modules/*/config.php');
require_once(dirname(dirname(__FILE__)).'/modules/core/components/Modules.php');

if (!empty($files)) {

	//настройки для каждого модуля отдельно
	foreach ($files as $file)
	{
		$moduleConfig = require($file);
		// $name         = preg_replace('#^.*/([^\.]*)\.php$#', '$1', $file);
		$name         = preg_replace('#^.*/modules/([^\.]*)/config\.php$#', '$1', $file);

		if (!empty($moduleConfig['import'])) 
			$config['import']     = CMap::mergeArray($config['import'], $moduleConfig['import']);
		if (!empty($moduleConfig['rules']))
			$config['rules']      = CMap::mergeArray($config['rules'], $moduleConfig['rules']);
		if (!empty($moduleConfig['components']))
			$config['components'] = CMap::mergeArray($config['components'], $moduleConfig['components']);
		if (!empty($moduleConfig['params']))
			$config['params']    = CMap::mergeArray($config['params'], $moduleConfig['params']);
		if (isset($moduleConfig['modules']))
			$config['modules']   = CMap::mergeArray($config['modules'], array($name => $moduleConfig['modules']));

	}

	foreach($submodulesConfigs as $file){
		$moduleConfig = require($file);

		$parent = preg_replace("#^.*/modules/([^\.]*)/modules/([^\.]*)/config\.php$#", '$1', $file);
		$name   = preg_replace("#^.*/modules/([^\.]*)/modules/([^\.]*)/config\.php$#", '$2', $file);

		if (!empty($moduleConfig['import'])) 
			$config['import']     = CMap::mergeArray($config['import'], $moduleConfig['import']);
		if (!empty($moduleConfig['rules']))
			$config['rules']      = CMap::mergeArray($config['rules'], $moduleConfig['rules']);
		if (!empty($moduleConfig['components']))
			$config['components'] = CMap::mergeArray($config['components'], $moduleConfig['components']);
		if (!empty($moduleConfig['params']))
			$config['params']    = CMap::mergeArray($config['params'], $moduleConfig['params']);
		
		if (isset($moduleConfig['modules']))
			$config['modules'][$parent]['modules'][$name] = $moduleConfig['modules'];
	}
}

Modules::init();