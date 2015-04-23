<?php
/**
 * Клас настроек Settings
 * 
 * @property integer    $id             - id шаблона
 * @property string     $title          - Название
 * @property string     $value          - значение
 * @property string     $cod            - код
 * @property integer    $pos            - позиция
 * @property string     $category       - категория
 * @property date       $created        - создано
 * @property integer    $id_creator     - Кто создал
 * @property date       $changed        - изменено
 * @property integer    $id_changer     - Кто изменил
 * 
 */

class Settings extends AR
{

    const PAGE_SIZE = 15;

    private static $_items = array();

    public $class = __CLASS__;

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
         return 'settings';
    }

    public function rules()
    {
        return array(
            array('title, code, value', 'required'),
            array('title, code, value, category', 'filter', 'filter'=>'trim'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'       => 'ID',
            'title'    => t('admin', 'Name'),
            'code'     => t('admin', 'Key'),
            'value'    => t('admin', 'Value'),
            'category' => t('admin', 'Category'),
        );
    }

    public function behaviors(){
        return CMap::mergeArray(parent::behaviors(), array(
            'sortable' => array(
                'class' => 'core.behaviors.sortable.SortableBehavior',
            ),
            'dateBehavior' => array(
                'class'           => 'DateBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'changed'
            ),
        ));
    }

    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('code', $this->code, true);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('category', $this->category, true);
        $criteria->compare('title', $this->title, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
              'pageSize'=>Common::getPagerSize(__CLASS__),
              'pageVar' => 'page'
            ),  
            
        ));
    }

    public function getBackUrl(){
        return app()->createUrl('core/settings/update', array('id'=>$this->id));
    }

    public static function getCategories($asMenu = false){
        $categories = Yii::app()->db->createCommand()
                ->from("{{settings}}")
                ->select('category')
                ->group('category')
                ->queryColumn();

        if(!$asMenu) return $categories;

        $result = '';
        
        $result .= CHtml::openTag('ul');
        foreach($categories as $category){
            $result .= CHtml::tag('li', ['data-id'=>$category], CHtml::link($category, '#'.$category));
        }
        $result .= CHtml::closeTag('ul');
        
        return $result;
    }


    //ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    public static function items()
    {
        if(empty(self::$_items))
            self::loadItems();
        return self::$_items;
    }
 
    public static function item($code, $default = null)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('code', $code);

        if(isset(app()->params['settings'][$code]))
            return  app()->params['settings'][$code];

        $item = self::model()->find($criteria);

        return $item? $item->value : $default;
    }
 
    private static function loadItems()
    {
        if(self::$_items) return self::$_items;
        $cacheKey = "settings";

        if(self::$_items = Yii::app()->cache->get($cacheKey)){
            return self::$_items;
        }

        $models = self::model()->findAll();

        foreach($models as $model)
            self::$_items[$model->code]=$model->value;

        $dependency = new CDbCacheDependency($sql = "SELECT MAX(changed) FROM ".self::model()->tableName());
        Yii::app()->cache->set($cacheKey, self::$_items, 0, $dependency);

        return self::$_items;
    }
}
?>