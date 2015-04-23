<?php

/**
 * LogNotify
 *
 * @property integer    $id 			- id
 * @property date 	    $created 		- Дата создания
 * @property integer    $id_creator		- Кто создал
 * @property date 	    $changed 		- дата изменения
 * @property integer    $id_changer 	- Кто изменил
 * @property string     $level 			- info | error | warning
 * @property string     $category 		- 
 * @property string     $message 		- 
 * @property integer    $status 		- 
 *
 */

class LogNotify extends AR
{
	const LEVEL_INFO		= 'info';
	const LEVEL_WARNING		= 'warning';
	const LEVEL_ERROR		= 'error';

	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	// отдаём соединение, описанное в компоненте db_old
	public function getDbConnection(){
		return Yii::app()->db;
	}

	// возвращаем имя таблицы вместе с именем БД
	public function tableName(){
		return 'log_notify';
	}

	public function rules(){
		return [
			['created', 'safe'],
			['level', 'email'],
			['category, message', 'filter', 'filter' => 'trim'],
			['status', 'numerical', 'integerOnly' => true],
			['created', 'safe'], // нужно для фильтрации в таблице
		];
	}

	public function scopes(){
		$alias = $this->getTableAlias();
		
		return [
			"info" => [
				"condition" => "{$alias}.level = :level",
				"params" => [":level" => self::LEVEL_INFO],
			],
			"warning" => [
				"condition" => "{$alias}.level = :level",
				"params" => [":level" => self::LEVEL_WARNING],
			],
			"error" => [
				"condition" => "{$alias}.level = :level",
				"params" => [":level" => self::LEVEL_ERROR],
			],
		];
	}

	public function attributeLabels(){
		return [
			'created' 	=> t('admin', 'Дата'),
			'level' 	=> t('admin', 'Уровень'),
			'category' 	=> t('admin', 'Категория'),
			'message' 	=> t('admin', 'Сообщение'),
		];
	}

	public function behaviors(){
		return CMap::mergeArray(parent::behaviors(), [
			'dateBehavior' => [
				'class' => 'DateBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'changed',
			]
		]);
	}

	public function search($criteria = false, $level = false){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$alias = $this->getTableAlias();

		if(!$criteria) $criteria = new SDbCriteria;

		if($level)
			$criteria->compare("{$alias}.level", $level);

		$this->compareDateRange($criteria, 'created', $this->created); //определен в DateBehavior
		$criteria->compare("{$alias}.category", $this->category, true);
		$criteria->compare("{$alias}.message", $this->message, true);

		return new CActiveDataProvider($this, [
			'criteria'	 => $criteria,
			'pagination' => [
				'pageSize' => Common::getPagerSize(__CLASS__),
				'pageVar'  => 'page'
			],
			
		]);
	}
}
?>