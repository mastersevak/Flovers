<?php 


/**
* SearchBehavior
*/
class SearchBehavior extends CActiveRecordBehavior
{
	
	public $scenario = null;

	public function afterConstruct($event){
		parent::afterConstruct($event);

		$this->init();
	}

	public function init(){
		if ($this->owner->scenario == 'search' || 
				$this->owner->scenario == $this->scenario) {

			$this->owner->unsetAttributes();

			if( ($data = request()->getParam(get_class($this->owner)) ) || 
				(!empty($_REQUEST) && ($data = $_REQUEST)) ) {

                $this->owner->attributes = $data;
            }
		}
	}
}