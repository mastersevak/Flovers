<?php

/**
 * History
 *
 * @property integer 	$id_object 		- ссылка на родителя
 * @property text 		$previous_data  - предыдущее состояние строки (json_encoded)
 * @property text 		$changed_data  	- состояние тех полей которые изменились (json_encoded)
 * 
 */

class History extends AR
{
	/**
	 * Model
	 * @param  $classname
	 * @return CModel
	 */
	public static function model($classname = __CLASS__){
		return parent::model($classname);
	}

	/**
	 * Правила валидации
	 * @return array
	 */
	public function rules() {
		return [
			['id_object', 'numerical', 'integerOnly' => true],
			['previous_data, changed_data, id_object', 'required']
		];
	}

	/**
	 * Behaviors
	 * @return array
	 */
	public function behaviors() {
		return CMap::mergeArray(parent::behaviors(), [
			'dateBehavior'	=> [
				'class'			  => 'DateBehavior',
				'createAttribute' => 'created',
			]
		]);
	}
}