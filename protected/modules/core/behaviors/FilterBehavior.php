<?php

/**
 * FilterBehavior
 *
 * используется для организации фильтров, и форм которые фильтруют данные
 *
 */

class FilterBehavior extends CActiveRecordBehavior {

	private static $counter = 0;
	private $criteria;

	public $savedFilters; //сохраненные фильтры
	public $who, $what, $date_from, $time_from, $date_to, $time_to; //для фильтров (только те переменные которых нет в полях)
	public $searchAttributes = ['who', 'what', 'date_from', 'time_from', 'date_to', 'time_to'];

	public $hiddenFields = [];

	public function getSafeAttributes(){
		return implode(', ', $this->safeAttributes);
	}

	public function getSearchAttributes(){
		$searchAttributes = array_unique(CMap::mergeArray($this->searchAttributes, $this->owner->searchAttributes));
		return implode(', ', $searchAttributes);
	}

	/**
	 * Фильтры для действий
	 */
	public function compareActions($criteria){
		$alias = $this->owner->getTableAlias();
		//Таблица истории
		$historyTable = $this->owner->tableName(). "_events_history";

		if($this->date_from || $this->date_to || $this->who || $this->what){
			list($whoField, $whenField, $actionField) = $this->owner->getFields($this->what);

		
			$condition = [];
			
			$dateFrom = false;
			if($this->date_from){
				list($_d, $_m, $_y) = explode("-", $this->date_from);
				$dateFrom = "$_y-$_m-$_d ".($this->time_from ? $this->time_from : "00:00:00");
			}
			$dateTo = false;
			if($this->date_to){
				list($_d, $_m, $_y) = explode("-", $this->date_to);
				$dateTo = "$_y-$_m-$_d ".($this->time_to ? $this->time_to : "23:59:59");
			}

			$ids = [];

			for($i = 0; $i < count($whoField); $i++){
				$_condition = [];
				$whoCondition = ''; 
				$whenCondition = '';


				if($this->who) { 
					if($this->owner->hasAttribute($whoField[$i])){
						$whoCondition = "{$alias}.{$whoField[$i]} = {$this->who}";
					}
				}

				if($dateFrom || $dateTo){
					if($this->owner->hasAttribute($whenField[$i])){
						if($dateFrom) 
							$whenCondition = "{$alias}.{$whenField[$i]} >= '{$dateFrom}'";
						
						if($dateTo){
							if($whenCondition) $whenCondition .= " AND ";
							$whenCondition .= "{$alias}.{$whenField[$i]} <= '{$dateTo}'";
						}

						$whenCondition = "({$whenCondition})";
					}
				}
				//если не найдены атрибуты в таблице, то ищем в historyTable
				$ids = array_merge($ids, $this->getEventsHistoryId($historyTable, $actionField[$i], $this->who, $dateFrom, $dateTo));
				
				if($whoCondition) $_condition[] = $whoCondition;
				if($whenCondition) $_condition[] = $whenCondition;

				if($_condition) $condition[] = "(" . implode(" AND ", $_condition) . ")";
			}

			if($condition)
				$criteria->addCondition(implode(" OR ", $condition));

			if($ids)
				$criteria->compare("{$alias}.id", $ids);
		}
	}

	//элементы интерфейса

	public function dropDownList($name, $data, $attributes = [], $label = false, $default = false){
		$result = '';
		if(in_array($name, $this->hiddenFields))
			return $result;

		$model = get_class($this->owner);
		
		//для dropdown с data-url
		if(isset($attributes['data-url']) && isset($attributes['model'])) {
 			$modelName = $attributes['model'];

			if(isset($attributes['multiple']) && $attributes['multiple']){
				if($this->owner->$name)
					foreach($this->owner->$name as $one){
						$data[$one] = $modelName::listData()[$one];
					}
			}
			else{
				if($this->owner->$name) {
					if(isset($attributes['depends'])){
						if($this->owner->$attributes['depends'])
							$data[$this->owner->$name] = $modelName::listData($this->owner->$attributes['depends'])[$this->owner->$name];
					}
					else
						$data[$this->owner->$name] = $modelName::listData()[$this->owner->$name];
				}
			}
		}

		if($label) $result .= CHtml::label($label, "{$model}_{$name}");

		$value = $this->_getValue($name, $attributes);

		$result .= UIHelpers::dropDownList("{$model}[{$name}]", $value, $data, $attributes);

		return $result;
	}

	public function datePicker($name, $attributes = [], $label = false, $htmlOptions = [],  $default = false){
		$result = '';
		$model = get_class($this->owner);

		if($label) $result .= CHtml::label($label, "{$model}_{$name}");

		$value = $this->_getValue($name, $attributes);

		if(isset($attributes['data-default-value'])){
			$htmlOptions['data-default-value'] = $attributes['data-default-value'];
			unset($attributes['data-default-value']);
		}

		$result .= UIHelpers::datePicker("{$model}[{$name}]", $value, $attributes, $htmlOptions);

		return $result;
	}

	public function timesList($name, $attributes = [], $label = false){
		$result = '';
		$model = get_class($this->owner);

		if($label) $result .= CHtml::label($label, "{$model}_{$name}");

		$value = $this->_getValue($name, $attributes);

		$result .= UIHelpers::timesList("{$model}[{$name}]", $value, $attributes);

		return $result;
	}

	public function textField($name, $attributes = [], $label = false){
		$result = '';
		if(in_array($name, $this->hiddenFields))
			return $result;

		$model = get_class($this->owner);

		if($label) $result .= CHtml::label($label, "{$model}_{$name}");

		$value = $this->_getValue($name, $attributes);

		$result .= CHtml::textField("{$model}[{$name}]", $value, $attributes);

		return $result;
	}

	public function hiddenField($name, $attributes = []){
		$result = '';
		if(in_array($name, $this->hiddenFields))
			return $result;

		$model = get_class($this->owner);

		$value = $this->_getValue($name, $attributes);

		$result = CHtml::hiddenField("{$model}[{$name}]", $value, $attributes);

		return $result;
	}

	public function checkBox($name, $value = 1, $attributes = [], $asArray = false, $label = "", $params = []){
		$result = '';
		if(in_array($name, $this->hiddenFields))
			return $result;

		$model = get_class($this->owner);
		$id = "{$model}_{$name}_".$this->getId();

		$selected = false;

		if(is_array($this->owner->$name)){
			$selected = in_array($value, $this->owner->$name);
		}
		else{
			if($this->owner->$name){
				if(is_numeric($this->owner->$name))
					$selected = (int)$this->owner->$name === (int)$value;
				else 
					$selected = $this->owner->$name === $value;	
			}
			elseif(isset($params['default']) && !$this->isSetFilters()){
				$selected = $params['default'];
			}
			
		}

		if(isset($params['default'])){
			$params['data-default-value'] = $params['default'];
		}
		unset($params['default']);

		// $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' checkbox' : 'checkbox';
		$params = CMap::mergeArray(['value'=>$value, 'id'=>$id], $params);
        $result .= CHtml::openTag('div', $attributes);
        $result .= CHtml::checkBox("{$model}[{$name}]" . ($asArray ? "[]" : ""), $selected, $params);
        $result .= CHtml::label($label, $id);
        $result .= CHtml::closeTag('div');

		return $result;
	}

	public function checkBoxList($name, $data, $attributes){
		$result = "";
		foreach($data as $value => $label){
			$result .= $this->checkBox($name, $value, $attributes, true, $label);
		}

		return "";
	}

	private function compare($field, $filter, $partialMatch=false, $operator='AND', $escape=true){
		$sign = '';
		//для случая когда используем $this->compare($criteria, 'price', '> pricelist_filter_trade_price_from');
		if(in_array($filter[0], ['>', '<', '!'])){
			$sign = $filter[0];
			$filter = trim(substr($filter, 1));
		}

		if($this->owner->$filter === null || $this->owner->$filter == "") return;

		$prefix = !$this->owner->hasAttribute($field) ? $this->getPrefix($filter) : $this->owner->getTableAlias().".";

		$value = $this->$filter;

		$this->criteria->compare($prefix.$field, !is_array($value) ? $sign.$value : $value, $partialMatch, $operator, $escape);
	}

	public function checkEmpty($criteria, $field, $operator='AND'){
		$criteria->addCondition($field . " is null or " . $field . " = ''", $operator);
	}

	public function checkNotEmpty($criteria, $field, $operator='AND'){
		$criteria->addCondition($field . " is not null and " . $field . " != ''", $operator);
	}

	private function _getValue($name, &$attributes){
		if(isset($this->owner->$name)){
			$value = $this->owner->$name;
		}elseif(isset($attributes['default']) && !$this->isSetFilters()){
			$value = $attributes['default'];
		}else{
			$value = "";
		}
		
		if(isset($attributes['default']))
			$attributes['data-default-value'] = $attributes['default'];
		
		unset($attributes['default']);

		return $value;
	}

	private function getPrefix($name){

		if($matches = $this->parseName($name)){

			$relation = $matches[0];
			$field = $matches[1];

			$this->criteria->with[] = $relation;

			if($this->owner->hasRelated($relation))
				$relation .= ".";
		}

		return "";
	}

	private function parseName($name){

		if(preg_match('#(.+)(?=_filter)_filter_(.+)#', $name, $matches))
			return [$matches[1], $matches[2]];

		return [false, false];
	}

	public function generateUrl($url = false){
		$model = get_class($this->owner);

		$arr = [];

		$attributes = $_GET + $_POST;
		foreach($attributes as $key => $value){
			if($key != Yii::app()->request->csrfTokenName &&
				$key != '_url') $arr[$key] = $value;
		}

		foreach($this->safeAttributes as $attribute){
			$arr["{$model}[{$attribute}]"] = $this->$attribute;
		}

		if(!$url) $url = request()->hostInfo . request()->url;
		$url = rtrim($url, '#');

		return $url . ($arr ? "?" . http_build_query($arr) : "");
	}

	private function getId(){
		return self::$counter++;
	}

	//Выбран ли какой нибудь из фильтров
	public function isSetFilters(){
		$searchAttributes = CMap::mergeArray($this->searchAttributes, $this->owner->searchAttributes);

		//Проверка на валидацию
		if(!$this->owner->validate(['id'])){
			if(Yii::app()->request->isAjaxRequest)
				Common::jsonSuccess(true, ['validateFilterRequired' => $this->owner->getErrors()]);
			else
				return false;
		}

		foreach($searchAttributes as $attribute){
			if(!empty($this->owner->$attribute)){
				return true;
			}
		}
	}
	//функция для поиска действий в _events_history таблицах
	public function getEventsHistoryId($historyTable, $field = false, $who = false, $dateFrom = false, $dateTo = false){
		$command = Yii::app()->db->createCommand()
						->select("id_object")
						->from($historyTable)
						->where('id_object > 0');
		//если указано действие
		if($field) $command->andWhere('field_name = :field', [':field' => $field]);
		//если указан кто
		if($who) $command->andWhere('id_user =:who', [':who' => $who]);
		//если указано даты
		if($dateFrom) $command->andWhere('date >= :dateFrom', [':dateFrom' => $dateFrom]);
		if($dateTo) $command->andWhere('date <= :dateTo', [':dateTo' => $dateTo]);

		return $command->queryColumn();
	}

}
