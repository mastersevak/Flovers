<?php

/**
* Model AR 
*/
Yii::import('core.interfaces.iConstantes');

class AR extends ActiveRecord implements iConstantes
{

	const BELONGS_TO='SBelongsToRelation';
	const HAS_ONE='SHasOneRelation';
	const HAS_MANY='SHasManyRelation';
	const MANY_MANY='SManyManyRelation';

	public $withRelatedObjects = []; //используется при CascadeUpdateBehavior, CascadeDeleteBehavior

	public $oldAttributes; //атрибуты модели до нового сохранения
	public $noSyncable; //переменная при установке которой true, модель не будет синхронизоваться с другой базой
	public $loadWithRelations; //указан ли 3ий параметр в функции loadModel

	public function init(){
		parent::init();

		$this->oldAttributes = new IteratableArray;
	}

	public function getByPk($id, $changedField = 'changed'){
		$cacheKey = "ar.{__CLASS__}.pk.{$id}";

		// if($result = Yii::app()->cache->get($cacheKey)){
		// 	return $result;
		// }

		$result = $this->findByPk($id);

		if(!$result) return false;

		try{
			// $dependency = new CDbCacheDependency($sql = "SELECT {$changedField} FROM ".$this->tableName().
			// 								" WHERE ".$this->tableSchema->primaryKey."={$id}");

			// Yii::app()->cache->set($cacheKey, $result, 0, $dependency);
		}
		catch(Exception $e){
			
		}

		return $result;
	}

	/*public function __get($attribute){

		// if($pos > 0){ //pos > 0
		// 	$relation = substr($attribute, 0, $pos);
			
		// 	if($this->owner->hasRelated($relation)){

		// 		$attribute = substr($attribute, $pos + 1);

		// 		$object = $this->owner->getRelated($relation);
		// 		if(!is_array($object)) return $object->$attribute;
		// 	}
			
		// }

		return parent::__get($attribute);
	}*/

	public function behaviors(){
		return CMap::mergeArray( parent::behaviors(), []);
	}

	//используется для классож которые имеют behavior Ml
	public function defaultScope()
	{
		$behaviors = $this->behaviors();
		
		if(array_key_exists('ml', $behaviors))
	    	return $this->ml->localizedCriteria();

	    return [];
	}

	public function afterFind(){
		parent::afterFind();

		if(!empty($this->attributes)){
			$this->oldAttributes = new IteratableArray;
			$this->oldAttributes->attributes = $this->attributes;	
		}
	}

	public function getPurifier(){
		$htmlpurifier = new CHtmlPurifier();
    	
    	$htmlpurifier->options = [
	    	'Attr.AllowedFrameTargets'=>["_blank"=>true],
	    	'HTML.AllowedComments' => ['pagebreak'],
	    	'Attr.EnableID' => true
	    	// 'HTML.Allowed' => 'a[href], p[class], br, u, b, em, strong, h3, img[src], img[class], div[class], ul[class], li[class]'
    	];

    	return $htmlpurifier;
	}


	public static function getRandom($modelClass, $limit = false, $criteria = false){

		if(!$criteria) $criteria = new SDbCriteria;

		$ar = $modelClass::model(); 

		$params = array('order' => new CDbExpression('RAND()'));
		
		if($limit)
			$params['limit'] = $limit;

		$criteria->mergeWith($params);
        
        if($limit == 1) $model = $ar->find($criteria);
        else $model = $ar->findAll($criteria);
        
        return $model;
	}

	public function getIsFiltered(){
		foreach($this->attributes as $attribute){
			if($attribute !== null) return true;
		}

		return false;
	}

	//Для сохранения указанных полей для модели
    public function edit($data = [], $validate = true, $keys = false){
    	
    	if(empty($data)) {
    		$this->addCustomErrorMessage('Нет данных для сохранения');
    		return false; 
    	}

    	//если нужно взять конкретные поля указав ключи
    	if($keys) $data = array_intersect_key($data, array_flip($keys));
    
    	$this->attributes = $data;

    	$fields = array_keys($data);
    	if($this->hasAttribute('changed')) {
    		$fields[] = 'changed';
    		if($this->hasAttribute('id_changer')) $fields[] = 'id_changer';
    	}

    	//если например установлен catchchangesBehavior и для него были определены атрибуты 
    	//которые меняются, но мы забыли в edit, указазать эти колонки, то следующие действия, 
    	//помогут, эти поля тоже включить в список для сохранения, 
    	$behaviors = $this->behaviors();
    	if(array_key_exists('catchChanges', $behaviors)){
    		if(isset($behaviors['catchChanges']['attributes']))
    			foreach($behaviors['catchChanges']['attributes'] as $key => $attr){
    				if(is_array($attr)){
    					
    					if($this->oldAttributes->$key != $this->$key) { 
    						$fields[] = $key;
	    					$dateField = isset($attr['dateField']) ? $attr['dateField'] : $key . '_date';
	    					$userField = isset($attr['userField']) ? $attr['userField'] : $key . '_user';

	    					if($this->hasAttribute($dateField) && !Common::isCLI()) $fields[] = $dateField;
	    					if($this->hasAttribute($userField) && !Common::isCLI()) $fields[] = $userField;
	    				}
    				}
    				else{
	    				if($this->oldAttributes->$attr != $this->$attr) { 
	    					$fields[] = $attr;
		    				if($this->hasAttribute($attr."_date") && !Common::isCLI()) $fields[] = $attr."_date";
	    					if($this->hasAttribute($attr."_user") && !Common::isCLI()) $fields[] = $attr."_user";
		    			}
    				}

    				
    			}
    	}

    	if(!$result = $this->save($validate, $fields))
    		return $this->setCustomErrorMessage($this->getErrors());

    	return true;
    }


    //empty scope
    public function emptyModel(){
    	$alias = $this->getTableAlias();
    	
    	$this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.".".$this->primaryKey()." = -1",
        ));

        return $this;
    }


    //get empty provider
   	public function emptyProvider(){
   		
   		$alias = $this->getTableAlias();

   		return new CActiveDataProvider($this, array(
			'criteria'=> ['condition' => $alias.".id = -1"],
			'pagination'=>array(
              'pageSize'=>Common::getPagerSize(get_class($this)),
              'pageVar' => 'page'
            ),
		));
   	}

   	public function beforeSearch($condition = false){
   		
   		$get = $_GET;
   		if(isset($get['page'])) unset($get['page']);

   		if(intval(request()->getParam('clearFilters')) == 1 || 
			(!request()->isAjaxRequest && empty($get)) || $condition) // count($_GET) > 1, так как page всегда в случае таблиц существует
			return $this->emptyProvider();

		return false;
   	}

   	/**
	 * Scope для получения позиций со связями
	 *
	 * Order::model()->_relatedPositions([
	 *			'pvar', 
	 *			'product' => [
	 *				'brand', 'bcategory', 'bproductpvars', 
	 *				'options' => ['variation']
	 *			]
	 *		])->findByPk($id)
	 *
	 * вместо использования 
	 *
	 * Order::model()->with(['positions' => [
	 * 			'with' => [
	 *			 	'pvar', 
	 *			  	'product' => [
	 *			  		'with' => [
	 *				  		'brand', 'bcategory', 'bproductpvars', 
	 *				   		'options' => ['with' => ['variation'] ]
	 *				   	]
	 *			     ]
	 *			]
	 *		])->findByPk($id)
	 */
	
	public function __call($name, $arguments){
		if(strpos($name, '_related') === 0){
			$with = [];
			$index = str_replace('_related', '', $name);
			$index[0] = strtolower($index[0]);

			$with[$index] = ['with' => $this->withRelations($arguments[0], $with)];
			$this->getDbCriteria()->mergeWith(compact('with'));
			return $this;
		}
		else
			return parent::__call($name, $arguments);
	}

	public function withRelations($relations, $array = false){
		if(!$array) $array = [];
		foreach($relations as $key => $relation){
			if(is_array($relation)){
				$array[$key] = ['with' => $this->withRelations($relation)];
			}
			else $array[] = $relation;
		}

		return $array;
	}

	/**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 *
	 * переопределяем класс для случаев когда модель вызвана 
	 * через loadModel, и модель новая, и в ней есть loadWithRelations
	 * 
	 */
	public function __get($name)
	{
		$result = parent::__get($name);
		if($result === null && $this->isNewRecord && 
			$this->loadWithRelations && in_array($name, $this->loadWithRelations)){
			$className = $this->getMetaData()->relations[$name]->className;
			return new $className; 
		}

		return $result;
	}

	/**
	 * Переопределям setAttributes, для автоматического присвоения 
	 * данных, в случае если модель была найдена через loadModel, с указанием 3го параметра
	 *
	 * $model->attributes = $_POST['ModelName'];
	 *
	 * или же
	 * $model->attributes = $_POST; //в данном случае если в массиве есть ключ 'ModelName', 
	 * то данные возьмуться из этого ключа, если же нет, то конечно и всего массива
	 *
	 * кроме того если есть loadWithRelations, то каждой связке тоже будут присвоены значения
	 * 
	 * 
	 */
	public function setAttributes($values, $safeOnly=true){

		$className = get_class($this);
		
		if(is_array($values)){
			$_mainArray = array_key_exists($className, $values) && is_array($values[$className]) ? $values[$className] : $values;
			
			$columns = $this->tableSchema->columns;
			//purify array (fix for time fields)
			foreach($_mainArray as $key => &$_v){
				if(array_key_exists($key, $_mainArray) && isset($columns[$key]) && 
					($columns[$key]->dbType == 'datetime' || $columns[$key]->dbType == 'time') &&  $_v == '') $_v = null;
			}

			parent::setAttributes($_mainArray, $safeOnly);
		}
		

		if($this->loadWithRelations){
			$relations = $this->relations();

			$loadWithRelations = [];
			foreach($this->loadWithRelations as $one){
				if(isset($relations[$one]) && ($relations[$one][0] == AR::HAS_ONE || $relations[$one][0] == AR::HAS_MANY))
					$loadWithRelations[] = $one;
			}	

			foreach($loadWithRelations as $relation){

				if(isset($relations[$relation])){
					$modelName = $relations[$relation][1];

					//случай когда пришли значения через POST, где есть ключ с имененем модели реляции
					if(array_key_exists($modelName, $values) && $values[$modelName]) { 
						switch($relations[$relation][0]){
							//сохранение при HAS_MANY
							case AR::HAS_MANY:
								$models = [];
									
								//new
								if(isset($values[$modelName]['new']))
									foreach($values[$modelName]['new'] as $_values){
										$newmodel = new $modelName;
										$newmodel->setAttributes($_values);
										$models[] = $newmodel;
									}

								//old
								if(isset($values[$modelName]['old']))
									foreach($values[$modelName]['old'] as $id => $_values){
										$model = $modelName::model()->findByPk($id);

										if($model){
											$model->setAttributes($_values);
											$models[] = $model;	
										}
										
									}

								if($models) $this->$relation = $models;

								break;
							//сохранение при HAS_ONE
							case AR::HAS_ONE:
								$this->$relation->setAttributes($values[$modelName]);
								break;
						}
					}
					else{ //когда в loadWithRelations есть данный relation, но через пост не пришли их значения
						switch($relations[$relation][0]){
							//сохранение при HAS_MANY
							case AR::HAS_MANY:
								break;
							//сохранение при HAS_ONE
							case AR::HAS_ONE:
								//если не приходят значения реляции при создании, чтобы автоматом, при создании модели создавалсь также реляция
								if($this->$relation->isNewRecord)
									$this->$relation = new $modelName;
							break;
						}
					}
				}
				
			}
		}

		$event = new CEvent;
		$this->onSetAttributes($event);
	}

	/**
	 * Переопределяем save, для возможности автоматического определения сохранения
	 * со своими связями
	 */
	public function save($runValidation=true, $attributes=null){
		$relations = $this->relations();


		if($this->loadWithRelations){
			
			$loadWithRelations = [];
			foreach($this->loadWithRelations as $one){
				if(isset($relations[$one]) && ($relations[$one][0] == AR::HAS_ONE || $relations[$one][0] == AR::HAS_MANY))
					$loadWithRelations[] = $one;
			}
			
			$behaviors = $this->behaviors();
			if(!isset($behaviors['withRelated'])){
				$this->attachBehavior('withRelated', ['class'=>'core.behaviors.WithRelatedBehavior']);
			}
			return $this->withRelated->save($runValidation, $loadWithRelations);
		}
		else{
			return parent::save($runValidation, $attributes);
		}
	}

	/**
     * Для использования мультиязычных полей, и показа полей в модальном окне
     */
    public function getFieldsValues(){
    	$attributes = $this->attributes;

    	if($this->isMultilangual){
    		$langs = param('languages');
    		$behaviors = $this->behaviors();
    		$defLang = param('defaultLanguage');
    		
    		$lAttributes = $behaviors['ml']['localizedAttributes'];

    		foreach($langs as $key => $lang){
    			foreach($lAttributes as $attr){
    				$suffix = "_".$key;

    				if($key == $defLang)
    					$attributes[$attr] = $this->{$attr.$suffix};
    				else 
    					$attributes[$attr.$suffix] = $this->{$attr.$suffix};
    				
    			}
    		}
    	}

    	return $attributes;
    }

    /**
     * Запрос который возвращает результат как массив данных
     * а не массив объектов
     *
     * но можно использовать только для одной таблицы
     */
    public function queryAll($condition='', $params=array(), $fields = false, $asListData = false){
    	$schema = Yii::app()->db->schema;

    	$criteria = $this->getCommandBuilder()->createCriteria($condition, $params);
    	$this->beforeFind();
		$this->applyScopes($criteria);

    	if($fields) $criteria->select = implode(',', $fields);

    	$command = $schema->commandBuilder->createFindCommand($schema->getTable($this->tableName()), $criteria, $this->getTableAlias());
    	
    	//если указана только одна колонка вернуть только значения данного поля
    	$result = $fields && count($fields) == 1 ? $command->queryColumn() : $command->queryAll();

    	return $asListData ? CHtml::listData($result, $fields[0], $fields[1]) : $result;
    }

	/**
	 * Events 
	 */
	public function onSetAttributes($event){

		$this->raiseEvent('onSetAttributes', $event);
	}

	/**
	 * Проверка, является ли модель мультиязычной
	 */
	protected function getIsMultilangual(){
		$behaviors = $this->behaviors();
		return array_key_exists('ml', $behaviors);
	}
}


class SBelongsToRelation extends CBelongsToRelation{

	public $deleteBehavior;
	public $updateBehavior;
	public $empty;

}

class SHasOneRelation extends CHasOneRelation{

	public $deleteBehavior;
	public $updateBehavior;
	public $empty;

}

class SHasManyRelation extends CHasManyRelation{
	
	public $deleteBehavior;
	public $updateBehavior;
	public $empty;

}

class SManyManyRelation extends CManyManyRelation{

	public $deleteBehavior;
	public $updateBehavior;
	public $empty;
	public $deleteSource;

}