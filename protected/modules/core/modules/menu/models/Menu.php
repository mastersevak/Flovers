<?php
/**
 * Menu
 *
 * модель для хранения меню со своими элементами
 *
 * @property  integer  $id                  - id
 * @property  date     $created             - дата создания
 * @property  integer  $id_creator     		- Кто создал
 * @property  date     $changed             - дата изменения
 * @property  integer  $id_changer     		- Кто изменил
 * @property  integer  $lft 
 * @property  integer  $rgt 
 * @property  smallint $level
 * @property  varchar  $slug 				- Именной ключ
 * @property  varchar  $url 
 * @property  smallint $root 
 * @property  varchar  $name 				- имя меню
 * @property  varchar  $icon 				- иконка
 * @property  tinyint  $enabled 			- активность
 * 
 */

class Menu extends Treelist
{
	/**
     * Multilingual fields
     */
    public $name;

	/**
	 * Model
	 * @param  $classname
	 * @return CModel
	 */
	public static function model($classname = __CLASS__){
		return parent::model($classname);
	}
  
    /**
     * Имя таблицы вместе с именем БД
     * @return string
    */
    public function tableName(){
        return '{{menu}}';
    }

    public function rules(){

		return CMap::mergeArray(parent::rules(), [

			//unique
			array('slug', 'unique', 'allowEmpty'=>true),
			//regexp
			array('slug', 'match', 'pattern'=>'/^[a-z0-9\-\_\/\.\(\)]+$/ui'),
			//size
			array('slug', 'length', 'max'=>255),
			
			array('url, containerTag, itemTag, activeCssClass, itemCssClass, itemTemplate, linkLabelWrapper, submenuWrapper, active, visible, items', 'filter', 'filter'=>'trim'),
			array('activateItems, activateParents, encodeLabel', 'numerical', 'integerOnly'=>true),

			/**
             * Поля из связанной таблицы menu_lang
             */
            ['name', 'filter', 'filter'=>'trim'],
            ['name', 'filter', 'filter' => [$this->purifier, 'purify'] ],
		]);
	}

	public function behaviors() {
	    return CMap::mergeArray(parent::behaviors(), [
	    	'slugger' => [
                'class' => 'core.behaviors.SlugBehavior',
                'sourceAttribute' => 'name',
            ],
	        'dateBehavior'	=> [
	            'class'			  => 'DateBehavior',
	            'createAttribute' => 'created',
	            'updateAttribute' => 'changed'
	        ],
	        'ml' => [
	        	'class' => 'core.behaviors.Ml',
                'langTableName' => 'menu_lang',
                'langForeignKey' => 'id_menu',
                'localizedAttributes' => array('name'), //attributes of the model to be translated
            ]
	    ]);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return CMap::mergeArray(parent::attributeLabels(), array(
			'slug'   						=> t('admin','Идентификатор меню'),
			'url'    						=> t('admin','Ссылка'),
			'activeCssClass'				=> 'activeCssClass',
			'itemCssClass'					=> 'itemCssClass',
			'itemTemplate'					=> 'itemTemplate',
			'submenuWrapper'				=> 'submenuWrapper',
			'linkLabelWrapper'				=> 'linkLabelWrapper',
			'visible'						=> 'visible',
			'active'						=> 'active',
		));		
	}

	public static function renderMenu($slug){
		$result = Yii::app()->cache->get(lang().".menu.".$slug);

		if(!$result) {
			$data = [];
			$data = Menu::model()->roots()->findByAttributes(['slug' => $slug]);
			
			if(!$data){
				$result = '';
			}
			else{
				$treeSMenuArray = $data->treeSMenuArray();
				$result = ['id' => 'menu-'.$slug, 'items' => $treeSMenuArray] + $data->getOptions($data, 1);
			}
			
			Yii::app()->cache->set(lang().".menu.".$slug, $result);
		}

		return $result ? app()->controller->widget('SMenu', $result, true) : '';
	}

	public function afterFind(){
		parent::afterFind();

		if($this->submenuHtmlOptions) $this->submenuHtmlOptions = CJSON::decode($this->submenuHtmlOptions);
		if($this->linkLabelWrapperHtmlOptions) $this->linkLabelWrapperHtmlOptions = CJSON::decode($this->linkLabelWrapperHtmlOptions);
		if($this->htmlOptions) $this->htmlOptions = CJSON::decode($this->htmlOptions);
		if($this->itemOptions) $this->itemOptions = CJSON::decode($this->itemOptions);
		if($this->submenuOptions) $this->submenuOptions = CJSON::decode($this->submenuOptions);
		if($this->linkOptions) $this->linkOptions = CJSON::decode($this->linkOptions);
	}

	//oчищаем кеш
    public function afterSave(){
    	parent::afterSave();
    	$root = Menu::model()->findByPk($this->root);
    	if($root){
    		Yii::app()->cache->delete(lang().".menu.".$root->slug);
    	}
    }

    public function afterDelete(){
    	parent::afterDelete();
    	
    	$root = Menu::model()->findByPk($this->root);
    	if($root){
    		Yii::app()->cache->delete(lang().".menu.".$root->slug);
    	}
    }


    public function treeSMenuArray(){
        $result = [];
        if(!$this->isLeaf()){
            
            foreach($this->children()->findAll() as $one){
                $result[$one->id] = [
                    'label' => $one->name,
                    'url' => $one->getUrl()
                ]; 

                $options = $this->getOptions($one);
                $result[$one->id] = $result[$one->id] + $options;

                if(!$one->isLeaf())
                    $result[$one->id]['items'] = $one->treeSMenuArray();
                elseif($one->items){

                	$itemsList = $one->items;
                	try {
				 		$evalResult = eval("return $itemsList;");
                		if(is_array($evalResult)){
                			$result[$one->id]['items'] = $evalResult;
                		}
					}
					catch (Exception $e) {
					 	$result[$one->id]['items'] = [];
					}
                }
            }  
        }

        return $result;
    }

	public function getUrl(){
		$url = '#';
		if($this->url){
			$this->url = explode(',', $this->url);
			$url = [];
			foreach($this->url as $one){
				if(strpos($one, '=') !== false){
					list($key, $value) = explode('=', trim($one));
					$url[$key] = $value;
				}
				else
					$url[] = trim($one);
			}
		}

		return $url;
	}

    /**
     * Функция для переопределения опций для SMenu
    */
    public function getOptions($one, $isRoot = 0){
    	
    	$result = [];
		if($one->activeCssClass)
    		$result['activeCssClass'] = $one->activeCssClass;
     	if($one->itemCssClass)
        	$result['itemCssClass'] = $one->itemCssClass;
        if($one->itemTemplate)
    		$result['itemTemplate'] = $one->itemTemplate;
    	if($one->linkLabelWrapper)
        	$result['linkLabelWrapper'] = $one->linkLabelWrapper;
        if($one->submenuWrapper)
    		$result['submenuWrapper'] = $one->submenuWrapper;
 		if($one->containerTag)
    		$result['containerTag'] = $one->containerTag;
    	if($one->itemTag)
    		$result['itemTag'] = $one->itemTag;

        
        if($one->htmlOptions){
     		foreach($one->htmlOptions as $item){
     			if($item['key'] && $item['value'])
     				$result['htmlOptions'][$item['key']] = $item['value'];
     		}
     	}
     	else{
     		$result['htmlOptions'] = [];
     	}

        if($one->linkLabelWrapperHtmlOptions){
     		foreach($one->linkLabelWrapperHtmlOptions as $item){
     			if($item['key'] && $item['value'])
     				$result['linkLabelWrapperHtmlOptions'][$item['key']] = $item['value'];
     		}
     	}
     	else{
			$result['linkLabelWrapperHtmlOptions'] = [];
     	}

     	if($one->submenuHtmlOptions){
     		foreach($one->submenuHtmlOptions as $item){
     			if($item['key'] && $item['value'])
     				$result['submenuHtmlOptions'][$item['key']] = $item['value'];
     		}
     	}
     	else{
     		$result['submenuHtmlOptions'] = [];
     	}


     	$result['activateItems'] = ($one->activateItems) ? true : false;
		$result['activateParents'] = ($one->activateParents) ? true : false;
		$result['encodeLabel'] = ($one->encodeLabel) ? true : false;

    	if(!$isRoot){
			if($one->itemOptions){
	     		foreach($one->itemOptions as $item){
	     			if($item['key'] && $item['value'])
	     				$result['itemOptions'][$item['key']] = $item['value'];
	     		}
	     	}
	     	else{
	     		$result['itemOptions'] = [];
	     	}
	     	
	     	if($one->linkOptions){
	     		foreach($one->linkOptions as $item){
	     			if($item['key'] && $item['value'])
	     				$result['linkOptions'][$item['key']] = $item['value'];
	     		}
	     	}
	     	else{
	     		$result['linkOptions'] = [];
	     	}

	     	if($one->submenuOptions){
	     		foreach($one->submenuOptions as $item){
	     			if($item['key'] && $item['value'])
	     				$result['submenuOptions'][$item['key']] = $item['value'];
	     		}
	     	}
	     	else{
	     		$result['submenuOptions'] = [];
	     	}

	     	if( $one->visible != ''){

	     		$result['visible'] = eval("return $one->visible;");
	     	}

	     	if($one->active != ''){

	     		$result['active'] = eval("return $one->active;");
	     	}

    	}
     	return $result;
    }

    public static function test(){
    	return [
    		[
    			'label' => 'test 1',
    			'url' => '#'
    		],
    		[
    			'label' => 'test 2',
    			'url' => '#'
    		],
    	];
    }

}
