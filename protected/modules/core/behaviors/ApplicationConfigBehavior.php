<?php
 
/**
 * ApplicationConfigBehavior is a behavior for the application.
 * It loads additional config parameters that cannot be statically 
 * written in config/main
 */
class ApplicationConfigBehavior extends CBehavior
{
	
	private $_default = [];

	public function __get($name){ 
		$settings = new SystemSettings;

		try{
			if(!isset(app()->params['languages']) && lang() != 'en')
				return $settings;
			else 
				return new $settings->helper;	
		}
		catch(Exception $e){
			die('Ошибка конфигурации');
		}

		
	}

	/**
	 * Declares events and the event handler methods
	 * See yii documentation on behavior
	 */
	public function events()
	{
		return array_merge(parent::events(), array(
			'onBeginRequest'=>'beginRequest',
		));
	}
 
	/**
	 * Load configuration that cannot be put in config/main
	 */
	public function beginRequest()
	{
		$default = $this->defaultSettings();
		
		//load settings from db, to params (overload config default settings)
		if(Yii::app()->db->getSchema()->getTable("{{settings}}")) {
			Yii::app()->params['settings'] = CMap::mergeArray(Yii::app()->params['settings'], Settings::items(), $default);
			Yii::app()->name = Settings::item('appname', app()->name);
		}		
	}

	private function defaultSettings(){
		$default = [
			'app' => $this->name,
			'languages' => app()->params['languages']
		];

		return $this->_default;
	}

	
}