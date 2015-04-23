<?php 
/**
 * EmailNotify
 *
 * @property integer    $id 			- id
 * @property date 	    $created 		- Дата создания
 * @property integer    $id_creator		- Кто создал
 * @property date 	    $changed 		- дата изменения
 * @property integer    $id_changer 	- Кто изменил
 * @property string     $to 			- 
 * @property string     $subject 		- 
 * @property string     $message 		- 
 * @property integer    $status 		- 
 *
 */

class Mail extends AR{

	const STATUS_NEW        = 0;
    const STATUS_SENT       = 1;
    const STATUS_ERROR      = 2;
    const STATUS_QUEUED     = 3;

	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function tableName()
    {
        return 'mail';
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

	public function rules()
    {
        return [
            ['email, message', 'required'],
            ['email, status', 'numerical', 'integerOnly' => true],
            ['status,created,subject, error', 'safe', 'on'=>'search']
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'id' 	  => 'ID',
            'email'   => 'Зл. почта',
            'message' => 'Сообщение',
            'subject' => 'Teма',
            'status'  => 'Статус',
            'created' => 'Дата',
            'changed' => 'Изменено',
            'error'   => 'Oшибка',   
        ];
    }

	public function search()
    {
        $criteria = new SDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('message', $this->message, true);
        $criteria->compare('subject', $this->subject);
        $criteria->compare('status', $this->status);
        $criteria->compare('error', $this->error, true);

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
        
        switch($this->status){
            case self::STATUS_ERROR :
                $url = url('/core/messaging/logs/resend');
                $btn = CHtml::link(CHtml::tag('i', ['class'=>'fa fa-sign-in mr5'], '')."ОШИБКА", '#', 
                    ['class' => $class . 'fbold bg-red c-white', 'data-model' => 'Mail', 'data-url' => $url, 'data-status' => 'error', 'data-id' => $this->id, 'data-wait' => 'Подождите',
                    'onclick' => "$.fn.logs('resend', $(this), 'email-gridview'); return false;"]);
                break;
            
            case self::STATUS_NEW :
                $btn = CHtml::tag('span', ['class' => $class . 'fbold c-white bg-blue'], CHtml::tag('i', ['class'=>'fa fa-clock-o mr5'], '').'НОВЫЙ');
                break;

            case self::STATUS_SENT :
                $btn = CHtml::tag('span', ['class' => $class.'fbold c-white bg-green'],  CHtml::tag('i', ['class'=>'fa fa-check mr5'], '').'ОТПРАВЛЕН');
                break;
            
            case self::STATUS_QUEUED :
                $btn = CHtml::tag('span', ['class' => $class.'fbold c-white bg-yellow'],  CHtml::tag('i', ['class'=>'fa fa-arrows-h mr5'], '').'В OЧЕРЕДИ');
            break;
        }

        return CHtml::tag('div', $params, $btn);
    }
}