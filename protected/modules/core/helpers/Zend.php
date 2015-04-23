<?php

/**
 * Various common functions
 */
class CFunc
{
	
	/**
	 * load zend components and autoloader
	 */
	public static function loadZend()
	{
		Yii::import('application.vendors.*');
		require_once 'Zend/Loader/Autoloader.php';
		spl_autoload_unregister(array('YiiBase','autoload'));
		spl_autoload_register(array('Zend_Loader_Autoloader','autoload'));
		spl_autoload_register(array('YiiBase','autoload'));
	}
	
	/**
	 * Display RSS Data
	 */
	public static function displayRss( $array, $type='rss' )
	{
		self::loadZend();
		
		$feed = Zend_Feed::importArray($array, $type);
		
		$feed->send();
		exit;
	}
	
	
}