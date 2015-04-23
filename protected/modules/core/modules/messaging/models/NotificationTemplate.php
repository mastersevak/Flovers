<?php
/**
 * @property int(11)        $id         - id шаблона
 * @property datetime       $created    - создано
 * @property datetime       $changed    - изменено
 * @property tinyint(1)     $type       - Тип
 * @property varchar(255)   $slug       - Именной ключ
 * @property varchar(255)   $title      - Название
 * @property varchar(255)   $subject    - Тема
 * @property text           $body       - Тело
 */

class NotificationTemplate extends AR
{
    const SMS        = 2;
    const EMAIL      = 1;


    public static $types = [1 => 'Email', 2 => 'Смс'];

    /**
     * Multilingual fields
     */
    public $title;
    public $subject;
    public $body;



    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Page the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    // отдаём соединение с базой
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы
    public function tableName(){
         return 'notification_template';
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
            array('key, title, subject, body', 'filter', 'filter'=>'trim'),
            
            //unique
            array('key', 'unique', 'allowEmpty'=>true),

            //regexp
            array('key', 'match', 'pattern'=>'/^[a-z0-9\-\_\/\.]+$/ui'),

            //type
            array('type', 'numerical', 'integerOnly'=>true),
            array('key, title', 'length', 'max'=>255),

            //safe validator
            array('id, key, title, subject', 'safe', 'on'=>'search'),
            
            //required
            array('key, title', 'required')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [

        ];
    }

    public function behaviors() {
        return CMap::mergeArray(parent::behaviors(), array(
            'dateBehavior' => array(
                'class' => 'DateBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'changed'
            ),
            'ml' => [
                'class' => 'core.behaviors.Ml',
                'langTableName' => 'notification_template_lang',
                'langForeignKey' => 'id_template',
                'localizedAttributes' => array('title', 'subject', 'body'), //attributes of the model to be translated
            ]
        ));
    }




    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'created'   => 'Дата создания',
            'changed'   => 'Дата изменения',
            'type'      => 'Тип шаблона',
            'key'      => 'Ключ',
            'title'     => 'Название',
            'subject'   => 'Тема',
            'body'      => 'Тело шаблона'
        );
    }
    
    public function scopes() {
        return [

        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($criteria = false, $type = false){
        $alias = $this->getTableAlias();

        if(!$criteria) $criteria = new SDbCriteria;

        if($type)
            $criteria->compare("{$alias}.type", $type, true);

        $criteria->compare('id', $this->id);
        
        $criteria->compare("{$alias}.type", $this->type);
        $criteria->compare("{$alias}.key", $this->key, true);
        $criteria->compare("{$alias}.title", $this->title, true);
        $criteria->compare("{$alias}.subject", $this->subject, true);
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$this->ml->modifySearchCriteria($criteria),
            'pagination'=>array(
                'pageSize'=>Common::getPagerSize(__CLASS__),
                'pageVar' => 'page'
            ),
        ));
    }
}
?>