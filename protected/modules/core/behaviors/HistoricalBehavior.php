<?php

/**
 * HistoricalBehavior
 *
 * behavior который позволяет сохранять предыдущую версию строки owner
 * в модели $this->model расширенную от History
 *
 * @property $bind  - массив, для указания реляций, которые сохранятся вместе с общими данными, при type = full
 * @property $model - модель, которая будет использоваться для хранения в таблице истории
 * 
 */
class HistoricalBehavior extends CActiveRecordBehavior
{
	public $model;
	public $bind = []; //массив в котором хранятся данные из связанных моделей

	public function afterConstruct($event){
		parent::afterConstruct($event);

		if(!$this->model) throw new Exception('Нужно установить модель для HistoricalBehavior');
	}

	//сохраняем историю
	public function afterSave($event){
		parent::afterSave($event);

		$this->createFullHistory();
	}

	//после открытия модели, если есть параметр history берем значения оттуда
	public function afterFind($event){
		parent::afterFind($event);

		if(!Common::isCLI() && ($history = request()->getParam('history')) )
			$this->getFullHistory($history);
	}

	/**
	 * Создаем полную историю модели, со всеми связками, если они указаны
	 */
	private function createFullHistory(){
		$owner = $this->owner;
		
		if(!$this->owner->isNewRecord){
			$old = $new = [];

			$old = [get_class($owner) => $owner->oldAttributes->attributes]; //предыдущие значения
			$_newData = $this->prepareNewData($this->owner);

			if(!empty($_newData))
				$new = [get_class($owner) => $_newData]; //измененные значения 
			
			
			if($this->bind){ 
				$relations = $this->owner->relations();

				//делаем тоже самое со связанными моделями
				foreach($this->bind as $relation){
					if(isset($relations[$relation])){
						switch($relations[$relation][0]){
							case AR::HAS_MANY:
								foreach($owner->$relation as $row){
									$old[$relations[$relation][1]][] = $row->oldAttributes->attributes;
									$_newData = $this->prepareNewData($row);
									if(!empty($_newData)) 
										$new[$relations[$relation][1]][] = $_newData;
								}
								break;
							
							case AR::HAS_ONE:
								$row = $owner->$relation;
								$old[$relations[$relation][1]] = $row->oldAttributes->attributes;
								
								$_newData = $this->prepareNewData($row);
								if(!empty($_newData)) 
									$new[$relations[$relation][1]] = $_newData;
								
								break;
						}
						
					}
				}
			}

			/**
			 * создаем историю, если в new не пусто
			 * это означает что модель реально изменили, 
			 * либо ее связанные модели
			 */
			if(!empty($new)){
				//создаем модель истории
				$model = new $this->model;
				$model->id_object = $owner->id;
				$model->previous_data = CJSON::encode($old);
				$model->changed_data = CJSON::encode($new);
				
				$model->save();		
			}
		}
	}

	/**
	 * Сравниваем и получаем новые и старые значения
	 */
	private function prepareNewData($owner){

		$newData = [];
		foreach ($owner->attributes as $key => $value) {
			$attribute = $key;

			if($owner->oldAttributes->$attribute != $owner->$attribute){
				$newData[$key]['old'] = $owner->oldAttributes->$attribute;
				$newData[$key]['new'] = $owner->$attribute;
			}
		}

		return $newData;
	}

	/**
	 * Заменяем значения полей модели, значениями пришедшими из 
	 * указанной истории
	 */
	private function getFullHistory($history){
		$history = CActiveRecord::model($this->model)->findByPk($history);

		if(!$history) throw new CHttpException(404, 'История не найдена');

		$data = CJSON::decode($history->attributes['previous_data']);
		$this->owner->setAttributes($data);
		$this->owner->oldAttributes->attributes = $data;

		//смотрим, есть ли для модели связанные модели
		if($this->bind){
			$relations = $this->owner->relations();

			$bind = [];
			
			foreach($this->bind as $one){
				if(isset($relations[$one]) && ($relations[$one][0] == AR::HAS_ONE || $relations[$one][0] == AR::HAS_MANY))
					$bind[] = $one;
			}	

			foreach($bind as $relation){

				if(isset($relations[$relation])){
					$modelName = $relations[$relation][1];

					if(array_key_exists($modelName, $data) && $data[$modelName]) {
						switch($relations[$relation][0]){
							//при HAS_MANY
							case AR::HAS_MANY:
								$models = [];
								
								if(isset($data[$modelName]))
									foreach($data[$modelName] as $_values){
										$_m = new $modelName;
										$model = $_m->findByPk($_values['id']);
										if($model){
											$model->setAttributes($_values);
											$model->oldAttributes->attributes = $_values;
											$models[] = $model;	
										}
									}

								if($models) $this->owner->$relation = $models;
								break;
							//при HAS_ONE
							case AR::HAS_ONE:
								$this->owner->$relation->setAttributes($data[$modelName]);	
								$this->owner->$relation->oldAttributes->attributes = $data[$modelName];	
								break;
						}
					}
				}
				
			} //foreach
		}
	}
}