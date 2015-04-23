<?php
/**
 * Photoalbum model
 *
 * @property integer 	$id        		- id
 * @property integer    $created        - создано
 * @property integer 	$id_creator		- Кто создал
 * @property integer    $changed        - изменено
 * @property integer 	$id_changer		- Кто изменил
 * @property date    	$date      		- дата
 * @property integer 	$status    		- статус пользователя
 * @property string  	$slug      		- Именной ключ
 * @property integer 	$thumbnail 		-
 * @property integer 	$pos       		- позиция
 *
 * Scopes
 * @defaultScope   	-
 * @scope active   	- 
 * @scope inactive 	- 
 * @scope last     	- 
 *
 * Functions
 * @function unbinded()   - 
 * @function getBackUrl() - 
 * @function getUrl()     - 
 * @function provider()   - 
 */

class Photoalbum extends AR
{

	public $title;

	public $image; //переменная для хранения картинки

	public $multilang = array('title');
	public $class = __CLASS__;

	public static $hasComments = false;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CategoryLang the static model class
	 */
	public static function model($classname = __CLASS__){
		return parent::model($classname);
	}

	// отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'photoalbum';
    }


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		
		return array(
			array('slug', 'filter', 'filter'=>'trim'),

			//unique
			array('slug', 'unique', 'allowEmpty'=>false),
			//regexp
			array('slug', 'match', 'pattern'=>'/^[a-z0-9\-\_]+$/ui'),
			
			//type
			array('image', 'file', 'types'=>implode(', ', param('upload_allowed_extensions')), 'allowEmpty' => true),
			array('status, thumbnail', 'numerical', 'integerOnly'=>true),
			array('date', 'date', 'format'=>'dd/MM/yyyy'),

			//required
			array('slug', 'required'),
			
			//save validator
			array('id, date', 'safe', 'on'=>'search'),

			/**
             * Поля из связанной таблицы PhotoalbumLang
             */
            array('title', 'required'), //это правило не указывать для связанной таблицы
			array('title', 'filter', 'filter'=>'trim'),
			array('title', 'length', 'max'=>255),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'lang'   => array(self::HAS_ONE, 'PhotoalbumLang', 'photoalbum_id', 'updateBehavior'=>true),
			'photo'  => array(self::BELONGS_TO, 'Photo', 'thumbnail', 'deleteBehavior'=>true),
			'photos' => array(self::MANY_MANY, 'Photo', 'tbl_related_photo(model_id, photo_id)', 
				'alias' => 'related', 'condition'=>'photos_related.model = :model', 
				'deleteBehavior' => true, 'deleteSource' => true, 
				// 'with' => array('lang'),
				'params'=>array(':model'=>__CLASS__)),
			'leisures'=>  array(self::MANY_MANY, 'Leisure', 'tbl_leisure_photoalbum(photoalbum_id, leisure_id)'),
		);
	}

	public function behaviors(){
		return CMap::mergeArray(parent::behaviors(), array(
			'sortable' => array(
	            'class' => 'SortableBehavior',
	        ),
	        'slugger' => array(
                'class' => 'core.behaviors.SlugBehavior',
                'sourceAttribute' => 'title',
            ),
			//для использования, getImage, getImageUrl, а так же для сохранения картинки ....
            'imageBehavior' => array(
                'class'  => 'ImageBehavior',
                'image'  => 'image',  //картинка
                'field'  => 'thumbnail', //поле для сохранения, ссылки на картинку
                'params' => param('images/photoalbumThumb') //массив с настройками для картинки
            ),
			'dateBehavior' => array(
				'class'           => 'DateBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'changed',
				'dateAttribute'   => 'date'
			)
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'         => 'ID',
			'status'     => t('admin', 'Статус'),
			'slug'       => t('admin', 'Ссылка'),
			'image'      => t('admin', 'Картинка'),
			'thumbnail'  => t('admin', 'Картинка'),
			'date'       => t('admin', 'Дата'),
			
			//fields from second table
			'title'      => t('admin', 'Заглавие'),
		);
	}

	//default scope
	public function defaultScope(){
		//if you use this in your defaultScope() you need to make sure it doesn't go into a recursive loop by:
		$alias = $this->getTableAlias( false, false );

		return array(
			'order'=>"{$alias}.pos DESC"
		);
	}

	public function scopes(){
		$alias = $this->getTableAlias();

		return array(
			'active' => array(
				'condition' => "{$alias}.status = :status",
				'params'    => array(':status' => self::STATUS_ACTIVE),
			),
			'inactive' => array(
				'condition' => "{$alias}.status = :status",
				'params'    => array(':status' => self::STATUS_INACTIVE),
			),
			'last' => array(
				'order' => "{$alias}.date DESC",
				'limit' => 1
			),
		);
	}

	//фотоальбомы не связанные с заведением
	public function unbinded($id){
		$criteria = new SDbCriteria;
		$criteria->select = 'photoalbum_id';
		$criteria->compare('leisure_id', $id);
		$ids = LeisurePhotoalbum::model()->queryAll($criteria, [], ['photoalbum_id']);

		$criteria = new SDbCriteria;
		$criteria->addNotInCondition('id', $ids);
		$this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}

	public function getBackUrl(){
		return app()->createUrl('core/photo/default/album/update', array('id'=>$this->id));
	}

	public function getUrl(){
		return app()->createUrl('core/photo/default/front/item', array('keyword'=> $this->slug));
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($criteria = false)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		if(!$criteria)
			$criteria = new SDbCriteria;

		$criteria->compare('t.id', $this->id); //против двусмысленности поля id
		$criteria->compare('status', $this->status);
		$criteria->compare('slug', $this->slug, true);
		$this->compareDate($criteria, 'date', $this->date); //определен в DateBehavior

		//searchWithRelated
		$criteria->together = true; //without this you wont be able to search the second table's data
		if($criteria->with){
			$criteria->with[] = 'lang';
		}
		else { 
			$criteria->with = array('lang');
		}

		$criteria->compare('lang.title', $this->title, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
			  'pageSize'=>Common::getPagerSize(__CLASS__),
			  'pageVar' => 'page',
			),
			'sort' => array(
				'attributes' => array(
					'date', 
					'status',
					'title' => array(
						'asc' => 'lang.title',
						'desc' => 'lang.title desc',
					)

				)
			)
		));
	}

	//photoalbum list
	public function provider($criteria = false){
		if(!$criteria)
			$criteria = new SDbCriteria;

		$criteria->order = 'date desc';
		$criteria->with = array('lang', 'photo');
		$criteria->compare('t.status', self::STATUS_ACTIVE);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=> $criteria->limit > 0 ? false : array(
			  'pageSize'=>$pageSize = self::PAGE_SIZE,
			  'pageVar' => 'page'
			),
		));
	}
	
}?>