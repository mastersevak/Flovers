<?php 


/**
* CatchChangesBehavior
*
* behavior который ловит ситуации изменения полей указанных 
* в переменной attributes, после чего меняет для данного поля, 
* другое связанное с ним поле (если оно есть) _user, _date
*
* например:
* в attributes указано поле: price
* мы поменяли его, и при сохранении behavior смотрит, если присутствует 
* поле price_date, price_user, то меняет и их. Если же для данного поля 
* мы ставили кастомные поля например price_change_date, price_change_user, 
* то он изменит и их. 
*
* пример использования:
*
*
public function behaviors(){
	return CMap::mergeArray(parent::behaviors(), array(
		'catchChanges' => [
			'class' => 'CatchChangesBehavior',
			'attributes' => [
				'count', 
				'cancel_mark' => [
					'dateField' => 'cancel_date',
					'userField' => 'cancel_user'
				],
			]
		]
	));
}
*
*
* Пример создания миграции для таких таблиц
public function safeUp()
{
	if(Yii::app()->db->getSchema()->getTable("order_events_history"))
			$this->dropTable("order_events_history");

	$this->createTable("order_events_history", [
			"id"	     => "int AUTO_INCREMENT",
			"id_user"    => "int UNSIGNED",
			"action"     => "varchar(10)",
			"id_object"  => "int UNSIGNED",
			"date"       => "datetime",
			"field_name" => "varchar(50)",
			"old_value"  => "varchar(255) CHARACTER SET UTF8",
			"new_value"  => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY id_user  (id_user)",
			"KEY action  (action)",
			"KEY id_object  (id_object)",
			"KEY date  (date)",
			"KEY field_name  (field_name)"
		]);
}

public function safeDown()
{
	if(Yii::app()->db->getSchema()->getTable("order_events_history"))
			$this->dropTable("order_events_history");
}
* 
* кроме того данный behavior также генерирует события 
* onИмяполяChange, например onPriceChange, которое можно
* будет уловить в дальнейшем, при надобности.
*
* также при генерации события передаются параметры в которых 
* указываются, старое и новое значение поля 
*/
class CatchChangesBehavior extends CActiveRecordBehavior
{
	public $attributes = [];

	public $saveHistory = false; //сохранять историю
	public $waitForSave = []; //данные для сохранения
	public $historyTable; 

	public function beforeSave($event){
		parent::beforeSave($event);
		$now = date('Y-m-d H:i:s');

		$owner = $this->owner;
		$behaviors = $owner->behaviors();

		foreach ($this->attributes as $key => $value) {
			
			$attribute = is_array($value) ? $key : $value;
			
			$isDateAttribute = (array_key_exists('dateBehavior', $behaviors) && 
				isset($behaviors['dateBehavior']['dateAttribute'])) && 
				(is_array($behaviors['dateBehavior']['dateAttribute']) ? 
					in_array($attribute, $behaviors['dateBehavior']['dateAttribute']) : 
					$behaviors['dateBehavior']['dateAttribute'] == $attribute);
			
			$changed = false;

			//проверка на несовпадение
			//если это дата, и дата указана в DateBehavior, то проверку делаем по другому
			if( $isDateAttribute && $owner->isDateChanged($attribute) ){
				$changed = true;	
			}
			elseif(!$isDateAttribute && $owner->oldAttributes->$attribute != $owner->$attribute){
				$changed = true;
			} 

			if($changed) {

				$dateField = is_array($value) && isset($value['dateField']) ? 
								$value['dateField'] : $attribute.'_date';

				if($owner->hasAttribute($dateField) && !Common::isCLI())
					$owner->$dateField = $now;

				$userField = is_array($value) && isset($value['userField']) ? 
								$value['userField'] : $attribute.'_user';
				
				if($owner->hasAttribute($userField) && !Common::isCLI())
					$owner->$userField = user()->id;

				if($this->saveHistory){
					$this->waitForSave[] = [
						'id_user' => !Common::isCLI() && !user()->isGuest ? user()->id : null, //кто делал изменения
						'action'  => $owner->isNewRecord ? 'create' : 'update',               //обновление или создание
						'id_object' => $owner->id,                                            //id модели
						'date'    => $now,                                                    //дата изменения
						'field_name' => $attribute,										      //поле которое изменялось
						'old_value' => $owner->oldAttributes->$attribute,	                  //старое значение поля
						'new_value' => $owner->$attribute                                     //новое значения поля
					];
				}

				if($owner->hasEvent('on'.$attribute.'Change')){
					
					$event = new CEvent($this);
					// событию можно присвоить для передачи любые параметры 
					// используя его свойство params
					$event->params = array(
						'old_'.$attribute => $owner->oldAttributes->$attribute,
						'new_'.$attribute => $owner->$attribute
					); 

					$owner->{'on'.$attribute.'Change'}($event);
				}
			}

		}

		return true;
	}


	/**
	 * Сохраняем историю
	 */
	public function afterSave($event){
		parent::afterSave($event);

		if($this->saveHistory && $this->waitForSave){
			$table = $this->historyTable ? $this->historyTable : $this->owner->tableName(). "_events_history";
			
			$builder = Yii::app()->db->schema->commandBuilder;
			$builder->createMultipleInsertCommand($table, $this->waitForSave)->execute();
		}
	}
}