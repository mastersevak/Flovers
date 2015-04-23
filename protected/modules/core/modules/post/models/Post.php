<?php 


/**
* Post model
*
* модель для представления статей
* новости, видеоматериалы ...
*
* с использованием похожих статей по тегам
*
* @property string  $id 		- id
* @property string  $created 	- создано
* @property string  $changed 	- изменено
* @property integer $status 	- статус пользователя
* @property string  $slug 		- slug
*
* post_lang
* @property string  $title 				- Заглавие
* @property string  $short_content 		- Короткое Описание
* @property string  $content 			- Описание
* @property string  $meta_title 		- Meta заглавие
* @property integer $meta_keywords 		- Meta ключевые слова
* @property string  $meta_description 	- Meta описание
* 
*/
class Post extends AR
{
	public $title;
	public $short_content;
	public $content;
	public $meta_title;
	public $meta_keywords;
	public $meta_description;

	//для временного хранения аватара
	public $image;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Page the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	// отдаём соединение
	public function getDbConnection(){
		return Yii::app()->db;
	}
 
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$purifier = $this->purifier;
		$purifier->options = [
			'Attr.AllowedFrameTargets'=>["_blank"=>true],
			'HTML.AllowedComments' => ['pagebreak'],
			'HTML.Allowed' => 'a[href], img[src]'
		];
		
		return [
			//filter
			['slug', 'filter', 'filter'=>'trim'],
			
			//unique
			['slug', 'unique', 'allowEmpty'=>true],
			//regexp
			['slug', 'match', 'pattern'=>'/^[a-z0-9\-\_\/\.]+$/ui'],

			//type
			['status', 'numerical', 'integerOnly'=>true],

			//image
			['image', 'file', 'types' => implode(', ', param('upload_allowed_extensions')), 'allowEmpty' => true],
			
			/**
			 * Поля из связанной таблицы Lang
			 */
			['title', 'required'], //это правило не указывать для связанной таблицы
			['title, short_content, content, meta_title, meta_keywords, meta_description', 'filter', 'filter'=>'trim'],
			['title, short_content, content, meta_title, meta_keywords, meta_description', 'filter', 'filter' => [$purifier, 'purify'] ],
			['title, meta_keywords, meta_description', 'length', 'max'=>255],
			
			// custom lengths
			['meta_title, short_content', 'length', 'max'=>70],
			['meta_description', 'length', 'max'=>170],
		];
	}

	/**
     * @return array relational rules.
     */
	public function relations(){
		return [
			'photo'	=> [self::BELONGS_TO, 'Photo', 'thumbnail', 'deleteBehavior'=>true],
		];
	}

	public function behaviors() {
		return CMap::mergeArray(parent::behaviors(), [
			//для использования, getImage, getImageUrl, а так же для сохранения картинки ....
			'imageBehavior' => [
				'class'  => 'ImageBehavior',
				'image'  => 'image',  //картинка
				'field'  => 'thumbnail', //поле для сохранения, ссылки на картинку
				'params' => param('images/news') //массив с настройками для картинки
			],
			'slugger' => [
				'class' => 'core.behaviors.SlugBehavior',
				'sourceAttribute' => 'title',
			],
			'dateBehavior' => [
				'class'=>'DateBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'changed',
			],
		]);
	}

	/**
	 * Функция которая возвращает масив с названиями labels для соответствующих полей
	 */
	public function attributeLabels() {
		return [
			'created'		 =>  t('back', 'Дата создания'),
			'changed'		 =>  t('back', 'Дата обновления'),
			'id_creator'	 =>  t('back', 'Создал'),
			'id_changer'	 =>  t('back', 'Изменил'),
			'title'			 =>  t('back', 'Название'),
			'slug'			 =>  t('back', 'Алиас'),
			'status'         =>  t('back', 'Статус'),
			'id_category' 	 =>  t('back', 'Kатегория'),
			'image'			 =>  t('back', 'Картинка'),
			'short_content'	 =>  t('back', 'Краткое описание'),
			'content'		 =>  t('back', 'Описание'),
			'description'	 =>  t('back', 'Oписание'),

			'meta_title'	 =>  t('back', 'Мета заглавие'),
			'meta_description'=>  t('back', 'Мета описание'),
			'meta_keywords'	 =>  t('back', 'Мета ключевые слова'),
		];
	}

	public function scopes() {
		return [
			'active' => [
				'condition' => 'status = :status',
				'params'    => [':status' => self::STATUS_ACTIVE],
			],
			'inactive' => [
				'condition' => 'status = :status',
				'params'    => [':status' => self::STATUS_INACTIVE],
			]
		];
	}

	public function getUrl(){
		return '';
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

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
			'pagination'=>[
			  'pageSize'=>Common::getPagerSize(__CLASS__),
			  'pageVar' => 'page'
			],
		]);
	}

}
