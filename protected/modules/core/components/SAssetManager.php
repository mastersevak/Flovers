<?php 


class SAssetManager extends CAssetManager
{
 
	public function publish($path, $hashByName=false, $level=-1, $forceCopy=false)
	{      
		$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
		return parent::publish($path,$hashByName,$level, $recreate);
	}

}