<?php 

/**
* UIHelpers
*/
class UIHelpers extends SWidget
{
	public static $dateRangeFilterOptions = [
					'locale'=>[
						'applyLabel'=>'Применить',
						'clearLabel'=>'Очистить',
						'fromLabel'=>'от',
						'toLabel'=>'до',
						'daysOfWeek'=>['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
						'monthNames'=>['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
						'firstDay'=>1],
					'format'=>'DD/MM/YYYY',
					'showDropdowns' => true
				];

	//рисует date picker который используется в основном в фильтрах таблиц
	public static function dateFilter($model, $name, $attributes = []){

		if($model->$name == 0)
			$model->$name = null;
		
		$attributes = CMap::mergeArray([
				'model'=>$model, 
				'attribute'=>$name,
				'pluginOptions'=>[
					'format'=>'dd/mm/yyyy', 
					'language'=>lang(),
					]
			], $attributes);

		return Yii::app()->controller->widget('yiiwheels.widgets.datepicker.WhDatePicker', $attributes, true);

	}

	//рисует daterange picker который используется в основном в фильтрах таблиц
	public static function dateRangeFilter($model, $name, $attributes = [], $htmlOptions = []){
		if($model->$name == 0)
			$model->$name = null;

		$htmlOptions = CMap::mergeArray([
			'class' => 'tcenter w170 custom-date-picker-input' //не убирать custom-date-picker-input, он используется в js
			], $htmlOptions);

		$attributes = CMap::mergeArray([
				'model'=>$model, 
				'attribute'=>$name,
				'pluginOptions'=>self::$dateRangeFilterOptions,
				'htmlOptions' => $htmlOptions
			], $attributes);

		return Yii::app()->controller->widget('yiiwheels.widgets.daterangepicker.WhDateRangePicker', $attributes, true);
	}

	//autocomplete в фильтрах
	public static function autocompleteFilter($model, $name, $filterModel, $attributes = []){
		return Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
						'model'=>$model,
						'attribute'=>$name,
						'htmlOptions'=>$attributes,
						'source' => Yii::app()->createUrl('/core/ajax/autocomplete?model='.$filterModel),
					), true);
	}

	//switcher IOS7
	public static function switcher($name, $checked = false, $attributes = []){

		cs()->registerScriptFile(Yii::app()->controller->rootAssetsUrl.'/plugins/ios-switch/switchery.js');
		cs()->registerCssFile(Yii::app()->controller->rootAssetsUrl.'/plugins/ios-switch/switchery.css');

		$id = isset($attributes['id']) ? $attributes['id'] : $name;

		cs()->registerScript('switchery_'.$id, "
			if($.fn.switchers == undefined) $.fn.switchers = {};
			$.fn.switchers['{$id}'] = new Switchery($('#{$id}').get(0), {color: '#0090d9'});");

		return CHtml::checkBox($name, $checked, $attributes);
	}

	//рисует date picker который в основном используется в фильтрах
	public static function datePicker($name, $value, $attributes = [], $htmlOptions = [], $arrows = true){
		$small = false;
		$additionalClass = actual($htmlOptions['class'], 'iblock');
		if(strpos($additionalClass, 'small') !== false){
			$additionalClass = str_replace('small', '', $additionalClass);
			$small = true;
		}
		
		unset($htmlOptions['class']);

		$htmlOptions = CMap::mergeArray([
			'class' => 'tcenter w100 custom-date-picker-input '.($small ? 'small' : '') //не убирать custom-date-picker-input, он используется в js
			], $htmlOptions);
		
		$attributes = CMap::mergeArray([
				'name'=>$name, 
				'value'=>$value,
				'pluginOptions'=>[
					'format'=>'dd-mm-yyyy', 
					'language'=>lang(),
					'weekStart' => 1,
					],
				'htmlOptions' => $htmlOptions
			], $attributes);

		if($arrows) { 
			cs()->registerScriptFile(Yii::app()->controller->widget('UIHelpers')->assetsUrl.'/js/datepicker.js');

			cs()->registerScript('custom_datepicker_'.$name, "$('input[name=\'{$name}\']').customDatePicker();");

			return CHtml::tag('div', ['class'=>'custom-date-picker ' . $additionalClass], 
				CHtml::link('', '#', ['class'=>'prev fa fa-caret-left']) .
				Yii::app()->controller->widget('yiiwheels.widgets.datepicker.WhDatePicker', $attributes, true) .
				CHtml::link('', '#', ['class'=>'next fa fa-caret-right'])
			);
		}
		else {
			return CHtml::tag('div', ['class'=>'custom-date-picker ' . $additionalClass], 
				Yii::app()->controller->widget('yiiwheels.widgets.datepicker.WhDatePicker', $attributes, true)
			);
		}
	}

	public static function hideSideBar(){
		return true;
	}

	public static function dropDownList($name, $select = '', $data = [], $htmlOptions = []){
		ob_start();
		$params = [
			'data' => $data,
		    'htmlOptions' => $htmlOptions
		];

		if($name instanceof CModel){
			$params['model'] = $name;
		    $params['attribute'] = $select;
		}
		else{
			$params['name'] = $name;
		    $params['value'] = $select;
		}

		Yii::app()->controller->widget('core.components.TbSelect', $params);
		$list = ob_get_contents();
		ob_end_clean();

		return $list;
	}

	/**
	 * Для списков с большим объемом данных
	 */
	public static function dropDownList2($name, $select = '', $data = [], $htmlOptions = []){
		ob_start();

		$params = [
			'data' => $data,
			'asDropDownList' => true,
		    'pluginOptions' => $htmlOptions
		];

		if($name instanceof CModel){
			$params['model'] = $name;
		    $params['attribute'] = $select;
		}
		else{
			$params['name'] = $name;
		    $params['value'] = $select;
		}

		Yii::app()->controller->widget('yiiwheels.widgets.select2.WhSelect2', $params);

		$list = ob_get_contents();
		ob_end_clean();

		return $list;
	}

	/**
	 * Рисует элемент contenteditable
	 * @param  [type] $value      [значение для показа]
	 * @param  [type] $attributes [атрибуты для contenteditable]
	 *
	 * возможные атрибуты
	 * ----------------------
	 * url - для указания другого url, который обработает запрос
	 * class - класс для оформления
	 * data-scenario - для указания сценария, для валидации
	 * data-searchfield - поле по которому находим объект (по умолчанию = id) 
	 * beforeUpdate - callback до изменения значения
	 * afterUpdate - callback после изменения значения
	 * другие htmlAttributes необходимые для поля
	 * 
	 * 
	 * объязательные атрибуты
	 * ----------------------
	 * data-model - имя модели 
	 * data-attribute - имя атрибута
	 * data-searchvalue - id модели (для поиска объекта)
	 * 
	 */
	public static function contentEditable($value, $attributes = []){
		$params = [
				'value' => $value,
				'attributes' => $attributes
			];

		if(isset($attributes['url'])) { 
			$params['url'] = $attributes['url'];
			unset($attributes['url']);
		}

		if(isset($attributes['beforeUpdate'])){
			$attributes['data-beforeUpdate'] = $attributes['beforeUpdate'];
			unset($attributes['beforeUpdate']);
		}

		if(isset($attributes['afterUpdate'])){
			$attributes['data-afterUpdate'] = $attributes['afterUpdate'];
			unset($attributes['afterUpdate']);
		}
		

		return app()->controller->widget('SContentEditable', $params, true);
	}

	// return array with years with given limit till current year
	public static function years($limit = 10, $reverse = false){
		$year = date('Y');
		$years = [];

		for ($i = $limit - 1; $i >= 0; $i--) { 
			$years[$year - $i] = $year - $i;
		}

		return $reverse ? array_reverse($years, true) : $years;
	} 

	// months
	public static function months(){
		$months = [
			1	=> "Январь",
			2	=> "Февраль",
			3	=> "Март",
			4	=> "Апрель",
			5	=> "Май",
			6	=> "Июнь",
			7	=> "Июль",
			8	=> "Август",
			9	=> "Сентябрь",
			10 	=> "Октябрь",
			11 	=> "Ноябрь",
			12 	=> "Декабрь",
		];

		return $months;
	}

	// weekdays
	public static function weekdays($lang = 'en'){
		$names = [
			'en' =>  [
				1 => 'Monday',
				2 => 'Tuesday',
				3 => 'Wednesday',
				4 => 'Thursday',
				5 => 'Friday',
				6 => 'Saturday',
				7 => 'Sunday'
			],

			'ru' => [
				1 => 'Понедельник',
				2 => 'Вторник',
				3 => 'Среда',
				4 => 'Четверг',
				5 => 'Пятница',
				6 => 'Суббота',
				7 => 'Воскресенье'
			]
		];

		return array_key_exists($lang, $names) ? $names[$lang] : [];
			
	}

	// datetimes
	public static function datetimes($diff = 'all'){
		$data = [
			"00:00",
			"00:30",
			"01:00",
			"01:30",
			"02:00",
			"02:30",
			"03:00",
			"03:30",
			"04:00",
			"04:30",
			"05:00",
			"05:30",
			"06:00",
			"06:30",
			"07:00",
			"07:30",
			"08:00",
			"08:30",
			"09:00",
			"09:30",
			"10:00",
			"10:30",
			"11:00",
			"11:30",
			"12:00",
			"12:30",
			"13:00",
			"13:30",
			"14:00",
			"14:30",
			"15:00",
			"15:30",
			"16:00",
			"16:30",
			"17:00",
			"17:30",
			"18:00",
			"18:30",
			"19:00",
			"19:30",
			"20:00",
			"20:30",
			"21:00",
			"21:30",
			"22:00",
			"22:30",
			"23:00",
			"23:30",
			"24:00",
			"24:00"
		];


		$result = [];
		foreach($data as $key => $val){
			switch ($diff) {
				case 'even':
					if(!($key%2)){
						$result[$val] = $val;
					}
					break;

				case 'odd':
					if($key%2){
						$result[$val] = $val;
					}
					break;
				
				case 'all':
					$result[$val] = $val;
					break;
			}
		}

		return $result;
	}

	/**
	 * Возвращает массив с временем исходя из переданного интервала
	 */
	public static  function getTimeInterval($interval = 1800, $diff = 'all'){
		$times = [];
		$time_first	 = strtotime("00:00");
		$time_second = strtotime("24:00");
		
		$key = 0; // for even/odd
		for ($i = $time_first; $i < $time_second; $i += $interval){
			$value = date('H:i', $i);

			switch ($diff) {
				case 'even':
					if(!($key%2)){
						$times[$value] = $value;
					}
					break;
				case 'odd':
					if($key%2){
						$times[$value] = $value;
					}
					break;
				
				case 'all':
					$times[$value] = $value;
					break;
			}

			$key++;
		}

		return $times;
	}

	/**
	 * Возвращает массив с интервалом дат
	 * @param  mixed	$from	- Первая дата, откуда начинается интервал(если передали false, то дата = сегодня)
	 * @param  integer	$days	- Количество дней
	 * @param  string	$format	- Формат даты
	 * @return array			- Массив с датами
	 */
	public static function getDateInterval($from = false, $days = 5, $format = 'd-m-Y'){
		if(!$from) $from = strtotime('now');
		else $from = strtotime($from);

		$dates = [];
		$firstDate = date($format, $from);
		$dates[$firstDate] = $firstDate;

		while($days > 1){
			$from += 86400;//Сутки
			$newDate = date($format, $from);
			$dates[$newDate] = $newDate;
			$days--;
		}

		return $dates;
	}

	public static function timesList($name, $value, $attributes = [], $diff = 'even'){
		$attributes = CMap::mergeArray([
					'empty' => "&nbsp;"
				], $attributes);

		$result = self::datetimes($diff);

		return self::dropDownList($name, $value, $result, $attributes);
	}
	
}