<?php

/**
 * Page model
 * 
 * @property string  $id 		- id
 * @property string  $created 	- создано
 * @property string  $changed 	- изменено
 * @property integer $status 	- статус пользователя
 * @property string  $slug 		- slug
 * @property string  $route 	                  
 *
 * page_lang
 * @property string  $title 			- Заглавие
 * @property string  $content 			- Описание
 * @property string  $meta_title 		- Meta заглавие
 * @property integer $meta_keywords 	- Meta ключевые слова
 * @property string  $meta_description 	- Meta описание
 * 
 * 
 * Scopes 
 * @scope active
 * @scope inactive
 * @scope routed
 *
 * Functions 
 * 
 * @function getUrl()
 * @function getBackUrl()
 * @function getPage()
 * 
 */

class Page extends AR
{
	const CACHE_ROUTE_KEY = 'page.routes';

    public $title;
    public $content;
    public $meta_title;
    public $meta_keywords;
    public $meta_description;

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
         return 'page';
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
			array('slug, route', 'filter', 'filter'=>'trim'),
			
			//unique
			array('slug', 'unique', 'allowEmpty'=>true),
			//regexp
			array('slug', 'match', 'pattern'=>'/^[a-z0-9\-\_\/\.]+$/ui'),
			array('route', 'match', 'pattern'=>'/^[a-z0-9\-\_\.]+$/ui'),

			//type
			array('status', 'numerical', 'integerOnly'=>true),

			//safe validator
			array('id', 'safe', 'on'=>'search'),
			
			/**
             * Поля из связанной таблицы page_lang
             */
            array('title', 'required'), //это правило не указывать для связанной таблицы
			array('title, content, meta_title, meta_keywords, meta_description', 'filter', 'filter'=>'trim'),
			array('title, content, meta_title, meta_keywords, meta_description', 'filter', 'filter' => [$this->purifier, 'purify'] ),
			array('title, meta_title, meta_keywords, meta_description', 'length', 'max'=>255),
		);
	}

	public function behaviors() {
		return CMap::mergeArray(parent::behaviors(), array(
			'dateBehavior' => array(
				'class'=>'DateBehavior',
				'createAttribute' => 'created',
				'updateAttribute' => 'changed',
			),
			'ml' => [
				'class' => 'core.behaviors.Ml',
	            'langTableName' => 'page_lang',
	            'langForeignKey' => 'id_page',
	            'localizedAttributes' => array('title', 'content', 'meta_title', 'meta_keywords', 'meta_description'), //attributes of the model to be translated
			]
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'created'          => t('admin', 'Дата создания'),
			'changed'          => t('admin', 'Дата изменения'),
			'status'           => t('admin', 'Статус'),
			'slug'            => t('admin', 'Ссылка'),
			'route'            => t('admin', 'Путь'),
			
			//fields from second table
			'title'            => t('admin', 'Название'),
			'content'          => t('admin', 'Содержание'),
			'meta_title'       => t('admin', 'Мета название'),
			'meta_keywords'    => t('admin', 'Ключевые слова'),
			'meta_description' => t('admin', 'Мета описание')
		);
	}
    
    public function scopes() {
        return array(
            'active' => array(
                'condition' => 'status = :status',
                'params'    => array(':status' => self::STATUS_ACTIVE),
            ),
            'inactive' => array(
                'condition' => 'status = :status',
                'params'    => array(':status' => self::STATUS_INACTIVE),
            ),
            'routed' => array(
            	'condition' => 'route is not null and route != ""'
            )
        );
    }

    public function getUrl(){
		return '';
	}

	public function getBackUrl(){
		return app()->createUrl('core/page/back/update', array('id'=>$this->id));
	}

	public function afterSave(){
		parent::afterSave();

		Yii::app()->cache->delete(self::CACHE_ROUTE_KEY);
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
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.slug', $this->slug, true);

		//searchWithRelated
		$criteria->compare('multilangPage.title', $this->title, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$this->ml->modifySearchCriteria($criteria),
			'pagination'=>array(
		      'pageSize'=>Common::getPagerSize(__CLASS__),
		      'pageVar' => 'page'
		    ),
		));
	}

	public static function getPage($slug, $field = false, $multilang = false){
		$_m = self::model();
		if($multilang) $_m->multilang();

		$model = $_m->find('slug = :slug', array(':slug'=>$slug));

		if($model && $model->content)
			$model->content = $model->purify($model->content);

		if(!$model) return !$field ? false : '';

		return !$field ? $model : $model->$field;
	}


	public function purify($content){
		//обработка ссылок
		return preg_replace_callback("%(href\s*=\s*[\"\'])([^\"\']+)%is", function($matches){

			if(strpos($matches[2], 'http') !== 0 && 
				strpos($matches[2], 'mailto') !== 0 && 
				strpos($matches[2], '#') !== 0) {


				if($matches[2]){
					$matches[2] = explode(',', trim($matches[2]));
					$url = [];
					foreach($matches[2] as $one){
						if(strpos($one, '=') !== false){
							list($key, $value) = explode('=', trim($one));
							$url[trim($key)] = trim($value);
						}
						else
							$url[] = trim($one);
					}
				}

				return $matches[1].CHtml::normalizeUrl($url);
			}
			else return $matches[1].$matches[2];
			
		}, $content);
		// 	foreach($matches[2] as $href){
		// 		if(strpos($href, 'http') === 0) continue;

		// 		$content = preg_replace("%(href\s*=\s*[\"\'])([^\"\']+)%is", "href=\"...\"", $content) ;
		// 	}
		// }

		// return $content;

		//обработка виджетов
		/*if(preg_match_all("%(.*?){{[a-z]:\[?(.+?)\]?}}(.*?)%is", $content, $matches)){
			foreach($matches[2] as $key => $widgetName){
				$par = $matches[3][$key];
				$par = str_replace(' ', ' ', $par); //здесь другой пробел, который ставит elrte
	            $par = preg_replace("#\s+#", " ", $par);

	            $content = str_replace(' ', ' ', $content); //здесь другой пробел, который ставит elrte
	            $content = preg_replace("#\s+#", " ", $content);

	            if(method_exists($this, 'widget'.strtolower($widgetName)) )
	            	return call_user_func([$this, 'widget'.strtolower($widgetName)], $content, $par);
	            else{
	            	return $this->widget($content, $par); 
	            }
	        }
		}*/
	}

	private function widgetHref($content, $par){
		dump([$content, $par], true);
		return '';
	}

	private function widgetYoutube($content, $par){
		return preg_replace("%(.*?)({{youtube:\[?".addcslashes($par, "?./")."\]?}})(.*?)%is", "$1".self::youtube($par)."$3", $content);
	}

	private function widget($content, $par){
		return $content;
	}	
	
}

