<?php

/**
 * This is the model class for table "Treelist".
 
  Объязательно переопределить $name в расширяемом классе
 *
 * The followings are the available columns in table 'Treelist':
 * @property string     $id
 * @property string     $lft
 * @property string     $rgt
 * @property integer    $level
 * @property integer    $root

 	public function up()
	{
		if(Yii::app()->db->getSchema()->getTable("{{tree}}")){
			$this->dropTable("{{tree}}");
		}

		$this->createTable("{{tree}}", array(
			"id"					=> "int AUTO_INCREMENT",
			"lft"				    => "int(10)",
			"rgt"			        => "int(10)",
			"level"		            => "smallint(6)",
			"root"			        => "smallint(6)",
			"PRIMARY KEY (id)"
			"KEY lft (lft)",
			"KEY rgt (rgt)",
			"KEY level (level)"
			"KEY root (root)"
		));
	}

	public function down()
	{
		if(Yii::app()->db->getSchema()->getTable("{{tree}}")){
			$this->dropTable("{{tree}}");
		}
	}
 */
class Treelist extends AR
{

	/**
	 * Returns the static model of the specified AR class.
	 * @return StoreCategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			//filters
			array('name', 'filter', 'filter'=>'trim'),
			array('name', 'filter', 'filter' => [$this->purifier, 'purify'] ),
			//required
			array('name', 'required'),
			//size
			array('name', 'length', 'max'=>255),
			// safe validator
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	public function behaviors()
	{
		return CMap::mergeArray(parent::behaviors(), array(
			'NestedSetBehavior'=>array(
				'class'=>'core.behaviors.NestedSet.NestedSetBehavior',
				'leftAttribute'=>'lft',
				'rightAttribute'=>'rgt',
				'levelAttribute'=>'level',
                'rootAttribute'=>'root',
                'hasManyRoots'=>true,
			),
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'    => 'ID',
			'lft'   => t('admin','Lft'),
			'rgt'   => t('admin','Rgt'),
			'level' => t('admin','Level'),
			'name'  => t('admin','Name'),
		);
	}

    /**
     * Возвращает массив для options, исходя из level
     */
    public static function listOptions($levels){
		$result = [];

		foreach ($levels as $id => $level)
			$result[$id] = ['data-level' => $level];

		return $result;
    }

    /**
     * Подготавливает listData, возвращая 2 массива:
     * 1. id => name
     * 2. id => level
     */
    public static function prepareListData($treelist){
		$result = [];
		
		foreach($treelist as $key => $value){
			if(is_numeric($key)){
				$result['list'][$key] = $value['label'];
				$result['levels'][$key] = $value['level'];

				if(isset($value['items'])){
					$result = CMap::mergeArray($result, self::prepareListData($value['items']));
				}
			}
		}

		return $result;
    }


    /**
     * Функция возвращающая все дочерние елементы с потомками в формате:
     * [
     *     id => [
     *         'поле' => 'значение',
     *         'поле' => 'значение',
     *     ],
     *     id => [
     *         'поле' => 'значение',
     *         'поле' => 'значение',
     *         'items' => [ //дочерние элементы
     *             ....
     *         ]
     *     ]
     * ]
     *
     */
    public function treeArray(){
        $result = [];
        if(!$this->isLeaf()){
            
            foreach($this->children()->findAll() as $one){
                $result[$one->id] = [
                	'id'    => $one->id,
                    'level' => $one->level,
                    'label' => $one->name,
                ]; 
                if(!$one->isLeaf())
                     $result[$one->id]['items'] = $one->treeArray();
            }  
        }

        return $result;
    }

    /**
     * возвращает айдишники потомков для категории с айди $id
     *
     * при указании $includeSelf, в результат входит также id
     *
     * @param  array $id
     */
    public static function descendantsIds($id, $includeSelf = false){
    	
    	$result = []; 
    	if(!$id) return $result;

    	$model = new static();
    	$criteria = new SDbCriteria;
    	$criteria->compare('id', $id);
    	$categories = $model->findAll($criteria);

    	foreach($categories as $category){
    		$result = CMap::mergeArray($result, $category->descendants()->queryAll('', [], ['id']));
    	}

    	if($includeSelf) { 
    		if(is_array($id)) {
    			array_walk($id, function(&$item){$item = (int) $item;});
    			$result = CMap::mergeArray($result, $id);
    		}
    		else 
    			$result[] = (int)$id;
    	}

    	return $result;
    }
  
  	/**
  	 * возвращает айдишники дочерних элементов для категории с айди $id
  	 * 
  	 * при указании $includeSelf, в результат входих также id
  	 */
    public static function childrenIds($id, $includeSelf = false){
    	
    	$result = []; 
    	if(!$id) return $result;
    	
    	$model = new static();
    	$criteria = new SDbCriteria;
    	$criteria->compare('id', $id);
    	$categories = $model->findAll($criteria);

    	foreach($categories as $category){
    		$result = CMap::mergeArray($result,  $category->children()->queryAll('', [], ['id']));
    	}

    	if($includeSelf) { 
    		if(is_array($id)) {
    			array_walk($id, function(&$item){$item = (int) $item;});
    			$result = CMap::mergeArray($result, $id);
    		}
    		else 
    			$result[] = $id;
    	}

    	return $result;
    }
}



