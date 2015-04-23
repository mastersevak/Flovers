<?php

/**
 * CLASS LOOKUP
 * Так как статус записи хранится в БД в виде числа, нам необходимо получить его текстовое представление для отображения пользователям. Для больших систем такое требование является довольно типичным.
 * 
 * Для хранения связей между целыми числами и их текстовым представлением, необходимым другим объектам данных, мы используем таблицу tbl_lookup. Для более удобного получения текстовых данных изменим модель Lookup следующим образом:
 *
 * Мы добавили два статичных метода: Lookup::items() и Lookup::item(). Первый возвращает список строк для заданного типа данных, второй — конкретную строку для заданного типа данных и значения.
 *
 * @property integer    $id     - id шаблона
 * @property string     $code   - код
 * @property string     $type   - Тип
 * @property integer    $pos    - позиция
 *
 */

class Lookup extends Ml
{

    const PAGE_SIZE = 15;

    private static $_items = array();

    /**
     * Multilingual fields
     */
    public $name;
    public $multilang = array('name');
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
         return 'lookup';
    }


    public function rules()
    {
        return array(
            array('name, type, code', 'required'),
            array('name, type, code', 'filter', 'filter'=>'trim'),
        );
    }

    public function relations()
    {
        return array(
            'lang' => array(self::HAS_ONE, 'LookupLang', 'lookup_id', 'updateBehavior'=>true),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => t('admin','Name'),
            'type' => t('admin','Type'),
            'code' => t('admin','Code'),
        );
    }

    public function behaviors(){
        return CMap::mergeArray(parent::behaviors(), array(
            'sortable' => array(
                'class' => 'core.behaviors.sortable.SortableBehavior',
            ),
        ));
    }

    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('type', $this->type, true);
        $criteria->compare('code', $this->code);
        //searchWithRelated
        $criteria->together = true; //without this you wont be able to search the second table's data
        $criteria->with = array('lang');
        $criteria->compare('lang.name', $this->name, true);
        

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
              'pageSize'=>Common::getPagerSize(__CLASS__),
              'pageVar' => 'page'
            ),  
            
        ));
    }

    public function getBackUrl(){
        return app()->createUrl('core/lookup/update', array('id'=>$this->id));
    }


    //ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    public static function items($type, $except = false)
    {
        if(!isset(self::$_items[$type]))
            self::loadItems($type, $except);
        return self::$_items[$type];
    }
 
    public static function item($type,$code)
    {
        if(!isset(self::$_items[$type]))
            self::loadItems($type);
        return isset(self::$_items[$type][$code]) ? self::$_items[$type][$code] : false;
    }
 
    private static function loadItems($type, $except = false)
    {
        self::$_items[$type]=array();
        $criteria = new CDbCriteria;
        $criteria->compare('type', $type);
        if($except != false) 
            $criteria->addNotInCondition('code', $except);

        $models=self::model()->with('lang')->findAll($criteria);

        foreach($models as $model)
            self::$_items[$type][$model->code]=$model->l_name;
    }
}
?>