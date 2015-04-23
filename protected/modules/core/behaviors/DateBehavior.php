<?php

Yii::import('zii.behaviors.CTimestampBehavior');
/**
* DateBehavior
*/
class DateBehavior extends CTimestampBehavior
{	

	public $setUpdateOnCreate = true;
	public $createAttribute;
	public $updateAttribute;
	public $dateAttribute;
	public $calendar = array('format' => 'd/m/Y', 'delimiter' => '/');
	public $onFind = true;

	protected static $map = array(
			'datetime'=>'Y-m-d H:i:s',
			'timestamp'=>'Y-m-d H:i:s',
			'date'=>'Y-m-d',
			'int'=>'U'
	);

	// после нахождения значения отформатировать дату
	public function afterFind($event){

		parent::afterFind($event);

		if(is_array($this->dateAttribute)){
			foreach($this->dateAttribute as $attribute)
				$this->_setValueOnFind($event->sender, $attribute);
		}
		else {
			$this->_setValueOnFind($event->sender, $this->dateAttribute);
		}	
	}

	// перед сохранением записи отформатировать даты
	public function beforeSave($event){

		parent::beforeSave($event);

		if(!Common::isCLI()){

			//id_user_created
			if($event->sender->getIsNewRecord()){
				if($this->createAttribute !== null)
					$attribute = 'id_creator';
			}
			else{ //id_user_changed
				if($this->updateAttribute !== null)
					$attribute = 'id_changer';
			}
			
			//set id_creator, id_changer on save
			if(isset($attribute) && $attribute && 
				($event->sender->hasAttribute($attribute) || property_exists($event->sender, $attribute))){
				$event->sender->{$attribute} = user()->id;

				if($event->sender->getIsNewRecord() && $this->updateAttribute != null && 
					($event->sender->hasAttribute('id_changer') || property_exists($event->sender, 'id_changer'))){
					$event->sender->id_changer = user()->id;
				}
			}
	
		}
		
		if($this->dateAttribute !== null){
			if(is_array($this->dateAttribute)){

				foreach($this->dateAttribute as $attribute)
					$this->_setValueOnSave($event->sender, $attribute);
			}
			else {
				$this->_setValueOnSave($event->sender, $this->dateAttribute);
			}
		}
		

		return true;

	}

	/**
	 * Установка значения при нахождении элемента
	 */
	private function _setValueOnFind($model, $attribute){
		if(!$model->hasAttribute($attribute) && !property_exists($model, $attribute)) return;

		if(empty($model->$attribute) || $model->$attribute == 0){
			$model->$attribute = null;
			return;
		}

		if($formatFrom = $this->getColumnFormat($attribute))
			$model->$attribute = $this->format($model->$attribute, $formatFrom, $this->calendar['format']);
		else
			$model->$attribute = null;
	}

	/**
	 * Установка значения при сохранении
	 */
	private function _setValueOnSave($model, $attribute){
		if(!$model->hasAttribute($attribute) && !property_exists($model, $attribute)) return;

		if(empty($model->$attribute)){
			$model->$attribute = null;
			return;
		}

		if($formatTo = $this->getColumnFormat($attribute))
			$model->$attribute = $this->format($model->$attribute, $this->calendar['format'], $formatTo);
		else{
			$model->$attribute = null;
		}


	}

	//найти формат колонки в базе
	private function getColumnFormat($attribute){
		$columnType = $this->getOwner()->getTableSchema()->getColumn($attribute)->dbType;

		if(preg_match('/^int\(\d+\)/', $columnType))
			$columnType = 'int';

		if(isset(self::$map[$columnType]))
			return self::$map[$columnType];

		return false;
	}

	public function isDateChanged($attribute){
		$owner = $this->owner;
		$old = $owner->oldAttributes[$attribute];

		$changed = false;

		if(is_array($this->dateAttribute))
			$changed = in_array($attribute, $this->dateAttribute);
		else
			$changed = $attribute == $this->dateAttribute;

		if($changed){
			$old = $this->format($old, $this->calendar['format'], $this->getColumnFormat($attribute));
		}

		return $old != $owner->$attribute;
	}

	//отформатировать в нужном формате
	public function format($value, $formatFrom, $formatTo){
		if($formatFrom == $formatTo) return $value;
		$date = DateTime::createFromFormat($formatFrom, $value);
		return $date ? $date->format($formatTo) : $value;
	}

	public function timestamp($attribute){
		$formatFrom = $this->getColumnFormat($attribute);
		if($formatFrom == 'U') return $this->owner->$attribute;

		return $this->format($this->owner->$attribute, $formatFrom, 'U');
	}

	//поиск по дате
	public function compareDate($criteria, $attribute, $value){
		
		if(empty($value)) return true;

		$dateto = null;
		$datefrom = null;
		
		if(in_array($value[0], ['>', '<'])) {
			if($value[0] == '>'){
				$value = ltrim($value, '>');
				$dateto = false;
			}elseif($value[0] == '<'){
				$value = ltrim($value, '<');
				$datefrom = false;
			}

			$value = trim($value);
		}

		if(empty($value)) return true;

		if(strpos($value, "-") !== FALSE) $delimiter = '-';
		elseif(strpos($value, "/") !== FALSE) $delimiter = '/';

		$parts = explode($delimiter, $value);

		if(count($parts) == 3){
			list($day, $month, $year) = $parts;
			if($datefrom === null) $datefrom = strtotime("$year-$month-$day 00:00:00");
			if($dateto === null) $dateto = strtotime("$year-$month-$day 23:59:59");
			$this->_compare($criteria, $attribute, $datefrom, $dateto);
			return true;	
		}

		return false;
		
	}

	//поиск по диапазону
	public function compareDateRange($criteria, $attribute, $value){
		
		if(empty($value)) return true;

		if(strpos($value, ' - ') === false) {
			$this->compareDate($criteria, $attribute, $value);
			return true;
		}

		list($from, $to) = explode(' - ', $value);

		if(strpos($from, "-") !== FALSE) $delimiter = '-';
		elseif(strpos($from, "/") !== FALSE) $delimiter = '/';

		$fromParts = explode($delimiter, $from);
		$toParts = explode($delimiter, $to);

		if(count($fromParts) == 3 && count($toParts) == 3){
			list($day, $month, $year) = $fromParts;
			$datefrom = strtotime("$year-$month-$day 00:00:00");

			list($day, $month, $year) = $toParts;
			$dateto = strtotime("$year-$month-$day 23:59:59");

			$this->_compare($criteria, $attribute, $datefrom, $dateto);
			return true;
		}

		return false;
		
	}

	/**
	 * Сам процесс поиска
	 */
	private function _compare($criteria, $attribute, $datefrom = false, $dateto = false){

		if(strpos($attribute, '.') !== false){
			$relation = substr($attribute, 0, strpos($attribute, '.'));
			$relations = $this->getOwner()->relations();

			if(isset($relations[$relation]))
				$owner = new $relations[$relation][1];
			else
				$owner = $this->getOwner();

			$columnType = $owner->getTableSchema()->getColumn(substr($attribute, strpos($attribute, '.') + 1))->dbType;	
			
		}
		else{
			$columnType = $this->getOwner()->getTableSchema()->getColumn($attribute)->dbType;
		}
		
		if(preg_match('/^int\(\d+\)/', $columnType)){

			if($datefrom) $criteria->compare($attribute, '>='.$datefrom);
			if($dateto) $criteria->compare($attribute, '<='.$dateto);
		}
		else {
			if($datefrom) $criteria->compare('UNIX_TIMESTAMP(' . $attribute . ')', '>='.$datefrom);
			if($dateto) $criteria->compare('UNIX_TIMESTAMP(' . $attribute . ')', '<='.$dateto);
		}

	}
}
?>