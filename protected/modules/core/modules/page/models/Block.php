<?php

/**
 * Block model
 *
 * @property string  	$id      		- id 
 * @property string  	$created 		- создано
 * @property integer 	$id_creator 	- Кто создал
 * @property string  	$changed 		- изменено
 * @property integer 	$id_changer		- Кто изменил
 * @property string  	$slug    		- slug
 * 
 * block_lang
 * @property string  $title 			- Заглавие
 * @property string  $content 			- Описание
 * 
 * Scopes
 *
 * @scope active -
 * @scope inactive -
 *
 * Functions
 *
 * @function getBackUrl() - 
 * @function getBlock()   -
 */

class Block extends AR
{

    public $title;
    public $content;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Page the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	// отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'block';
    }


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//filter
			array('slug', 'filter', 'filter'=>'trim'),
			
			//unique
			array('slug', 'unique', 'allowEmpty'=>false),
			//regexp
			array('slug', 'match', 'pattern'=>'/^[a-z0-9\-\_]+$/ui'),

			//required
			array('slug', 'required'),
			//safe validator
			array('id', 'safe', 'on'=>'search'),

			/**
             * Поля из связанной таблицы block_lang
             */
            array('title', 'required'), //это правило не указывать для связанной таблицы
			array('title, content', 'filter', 'filter'=>'trim'),
			array('title, content', 'filter', 'filter' => [$this->purifier, 'purify']),
			array('title', 'length', 'max'=>255),
			
		);
	}

	public function behaviors() {
		return CMap::mergeArray(parent::behaviors(), array(
			'slugger' => array(
                'class' => 'core.behaviors.SlugBehavior',
                'sourceAttribute' => 'title',
            ),
			'dateBehavior' => array(
				'class'=>'DateBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'changed',
			),
			'ml' => [
				'class' => 'core.behaviors.Ml',
	            'langTableName' => 'block_lang',
	            'langForeignKey' => 'id_block',
	            'localizedAttributes' => array('title', 'content'), //attributes of the model to be translated
			]
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'created' => t('admin', 'Дата создания'),
			'changed' => t('admin', 'Дата изменения'),
			'status'  => t('admin', 'Статус'),
			'slug'   => t('admin', 'Ссылка'),
			
			'title'   => t('admin', 'Заглавие'),
			'content' => t('admin', 'Содержание'),
		);
	}
    
    public function scopes() {
        return array(
            'active' => array(
                'condition' => 'status = :status',
                'params' => array(':status' => self::STATUS_ACTIVE),
            ),
            'inactive' => array(
                'condition' => 'status = :status',
                'params' => array(':status' => self::STATUS_INACTIVE),
            ),
        );
    }

	public function getBackUrl(){
		return app()->createUrl('core/page/blocks/update', array('id'=>$this->id));
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new SDbCriteria;
		
		$criteria->compare('t.id', $this->id); //против двусмысленности поля id
		$criteria->compare('t.slug', $this->slug, true);

		//searchWithRelated
		$criteria->compare('multilangBlock.title', $this->title, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $this->ml->modifySearchCriteria($criteria),
			'pagination' => array(
				'pageSize' => Common::getPagerSize(__CLASS__),
				'pageVar'  => 'page'
		    ),
		));
	}

	public static function getBlock($slug, $field = 'content', $multilang = false){
		$_m = self::model();
		if($multilang) $_m->multilang();

		$model = $_m->find('slug = :slug', array(':slug'=>$slug));
		
		if(!$model) return !$field ? false : '';

		return !$field ? $model : $model->$field;
	}

}

