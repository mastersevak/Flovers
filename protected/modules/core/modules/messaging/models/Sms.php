<?php

/**
 * @property int(10)        $id             - ID
 * @property datetime       $created        - Дата, когда создали смс сообщение
 * @property int(10)        $id_creator     - Пользователь, создавший смс сообщение
 * @property datetime       $changed        - Дата, когда изменили смс сообщение
 * @property int(10)        $id_changer     - Пользователь, изменивший смс сообщение
 * @property int(10)        $id_message     - ID смс сообщения
 * @property varchar(50)    $phone          - Телефонный номер отправителя
 * @property text           $body           - Тело смс сообщения
 * @property tinyint(1)     $queue_status   - Статус очереди
 * @property int(10)        $id_error       - ID ошибки отправки
 */

// TODO: NOTE: возможно есть смысл добавить provider-name
class Sms extends AR
{
    const STATUS_NEW        = 0;
    const STATUS_SENT       = 1;
    const STATUS_ERROR      = 2;
    const STATUS_QUEUED     = 3; // в очереди, может отправляться только в рабочее время

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'sms';
    }

    public function behaviors() {
        return [
            'dateBehavior' => [
                'class'           => 'DateBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'changed',
            ]
        ];
    }

    public function getStatusName()
    {
        $data = $this->getStatusList();
        return array_key_exists($this->status, $data) ? $data[$this->status] : '';
    }

    public function getStatusList()
    {
        return [
            self::STATUS_NEW        => 'новый',
            self::STATUS_SENT       => 'отправлен',
            self::STATUS_ERROR      => 'ошибка',
            self::STATUS_QUEUED     => 'в очереди',
        ];
    }

    public function rules()
    {
        return [
            ['phone, body', 'required'],
            ['phone, queue_status', 'numerical', 'integerOnly' => true],
            ['phone', 'length', 'max' => 26],
            ['body', 'length', 'max' => 500],
            ['queue_status,created', 'safe', 'on'=>'search']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_message' => 'Идентификатор сообщения',
            'phone' => 'Телефон',
            'body' => 'Сообщение',
            'queue_status' => 'Статус очереди',
            'created' => 'Дата',
            'changed' => 'Изменено'
        ];
    }

    public function search()
    {
        $criteria = new SDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('id_message', $this->id_message,true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('body', $this->body, true);
        $criteria->compare('queue_status', $this->queue_status);
        $this->compareDateRange($criteria, 'created', $this->created);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => Common::getPagerSize(__CLASS__),
                'pageVar'  => 'page'
            ],
        ));
    }

    /**
    * Рисует кнопку статуса
    */
    public function queueStatus(){
        $class = 'btn btn-mini block ';

        $params = [];

        $btn = '';
        
        switch($this->queue_status){
            case self::STATUS_ERROR :
                $url = url('/core/messaging/logs/resend');
                $btn = CHtml::link(CHtml::tag('i', ['class'=>'fa fa-sign-in mr5'], '')."ОШИБКА", '#', 
                    ['class' => $class . 'fbold', 'data-model' => 'Mail', 'data-url' => $url, 'data-status' => 'error', 'data-id' => $this->id, 'data-wait' => 'Подождите',
                    'onclick' => "$.fn.logs('resend', $(this), 'sms-gridview'); return false;"]);
                break;
            
            case self::STATUS_NEW :
                $btn = CHtml::tag('span', ['class' => $class . 'fbold c-black'], CHtml::tag('i', ['class'=>'fa fa-clock-o mr5'], '').'НОВЫЙ');
                break;

            case self::STATUS_SENT :
                $btn = CHtml::tag('span', ['class' => $class.'fbold c-black'],  CHtml::tag('i', ['class'=>'fa fa-check mr5'], '').'ОТПРАВЛЕН');
                break;
            
            case self::STATUS_QUEUED :
                $btn = CHtml::tag('span', ['class' => $class.'fbold c-black'],  CHtml::tag('i', ['class'=>'fa fa-arrows-h mr5'], '').'В OЧЕРЕДИ');
            break;
        }

        return CHtml::tag('div', $params, $btn);
    }
}