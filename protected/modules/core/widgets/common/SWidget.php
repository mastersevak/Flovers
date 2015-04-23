<?php 


class SWidget extends CWidget{

	public $assetsUrl;

	public function init(){
		parent::init();

		if ($this->assetsUrl === null) {
			$reflector = new ReflectionClass(get_class($this));

			$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;

			$assetsPath = dirname($reflector->getFileName()).DS.'assets';

			if(is_dir($assetsPath))
				$this->assetsUrl = assets($assetsPath, false, -1, NULL);
		}
	}
}
