<?php 


/**
* SMenu
*/
class SMenuComponent extends CApplicationComponent
{
	
	const DEFAULT_CACHE_TIME = 5184000;

    public $data;
    public $cachingTime;

    public $options = array();
    public $serviceName;
    public $employeeName;


    public $uniqueId;
    public $action;
   	public $module;

    public function init()
    {
        parent::init();

        $this->uniqueId = Yii::app()->controller->uniqueId;
        $this->action = Yii::app()->controller->action;
        $this->module = Yii::app()->controller->module;

        $this->cachingTime = Yii::app()->settings->get('Core', 'cachingTime', self::DEFAULT_CACHE_TIME);
    }

    public function load($submenu = '')
    {
        $cacheName = $this->getMenuName() . $submenu;

        $this->data = Yii::app()->cache->get($cacheName);

        if($this->data === false) {
            $this->data = call_user_func(array($this, $submenu . 'Menu'));
            Yii::app()->cache->set($cacheName, $this->data, $this->cachingTime);
        }

        return $this->data;
    }

    public function clearCache($submenu = '')
    {
        Yii::app()->cache->delete( $this->getMenuName() . $submenu);
    }
}