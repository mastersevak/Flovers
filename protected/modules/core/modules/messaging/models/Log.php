<?php 
/**
 * EmailNotify
 *
 * @property integer    $id 			- id
 * @property date 	    $level 		    - Уровень ошибки
 * @property integer    $category		- Категория
 * @property date 	    $logtime		- дата создание
 * @property integer    $message 	    - Сообщение
 *
 */

class Log extends AR{

    const LEVEL_INFO        = 'info';
    const LEVEL_WARNING     = 'warning';
    const LEVEL_ERROR       = 'error';

	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function tableName()
    {
        return 'log';
    }
	
	public function rules()
    {
        return [
            ['level,category,message,logtime', 'safe', 'on'=>'search']
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'logtime'   => t('admin', 'Дата'),
            'level'     => t('admin', 'Уровень'),
            'category'  => t('admin', 'Категория'),
            'message'   => t('admin', 'Сообщение'),
        ];
    }
    
    public function behaviors(){
        return CMap::mergeArray(parent::behaviors(), [
            'dateBehavior' => [
                'class' => 'DateBehavior',
                'createAttribute' => 'logtime',
            ]
        ]);
    }

    public function search($criteria = false, $level = false){
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $alias = $this->getTableAlias();

        if(!$criteria) $criteria = new SDbCriteria;

        if($level)
            $criteria->compare("{$alias}.level", $level, true);

        $this->compareDateRange($criteria, 'logtime', $this->logtime); 
        $criteria->compare("{$alias}.category", $this->category, true);
        $criteria->compare("{$alias}.message", $this->message, true);

        return new CActiveDataProvider($this, [
            'criteria'   => $criteria,
            'pagination' => [
                'pageSize' => Common::getPagerSize(__CLASS__),
                'pageVar'  => 'page'
            ],
            
        ]);
    }
}