<?php 

/**
 * Status Behavior
 * поведение для классов AR, которое меняет статус, и выдает scope status
 */
 class StatusBehavior extends CActiveRecordBehavior
 {
 	
 	public $flagField = 'status';

 	/**
 	 * функция которая устанавливает статус
 	 * пример использования:
 	 * 
 	 * $model = User::model()->status(User::STATUS_ACTIVE)->findAll();
 	 */
 	public function status( $value ){

 		$criteria = $this->getOwner()->getDbCriteria();
 		$criteria->compare($this->flagField, $value);

 		return $this->getOwner();
 	}

 	public function setStatus( $value ){
 		$model = $this->getOwner();
 		$model->{$this->flagField} = $value;
 		return $model->save(true, array($this->flagField));
 	}
 } 

 ?>