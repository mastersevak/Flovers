<?php
/**
 * UserAuth
 *
 * @property integer    $id                 - id 
 * @property integer    $user_id            - id пользователь
 * @property string     $service_name       -
 * @property string     $service_user_id    -
 * @property string     $service_user_name  -
 * @property string     $service_user_url   -
 * @property string     $service_user_pic   -
 * @property string     $service_user_email -
 * @property date       $created            - создано
 * @property date       $changed            - изменено
 */

class UserAuth extends AR
{
     
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    // отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'user_auth';
    }
    
    /**
     * Правила валидации
     */
    public function rules() {

        /**
         * Правила для этой модели описаны в User
         */
        
        return array(

            /**
             * Общие правила
             */
            
            //filter trim
            array('service_name, service_user_id, service_user_name, service_user_url, service_user_pic, service_user_email', 'filter', 'filter'=>'trim'),
            //filter purify
            array('service_name, service_user_id, service_user_name, service_user_url, service_user_pic, service_user_email',
                    'filter', 'filter' => [$this->purifier, 'purify']),

            array('id_user, service_name, service_user_id', 'required'),
            
            //type
            array('id_user, created, changed', 'numerical', 'integerOnly'=>true),
        );
    }

    //только для данного проекта
    public function relations(){
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'id_user')
        );
    }

    public function behaviors() {
        return CMap::mergeArray(parent::behaviors(), [
            'dateBehavior'  => [
                'class'           => 'DateBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'changed'
            ]
        ]);
    }

    /**
     * Функция которая возвращает масив с названиями labels для соответствующих полей
     */
    public function attributeLabels() {
        
        return array(
            "service_name"          => t('user', 'Название соц. сети'),
            "service_user_id"       => t('user', 'Id пользователя'),
            "service_user_name"     => t('user', 'Имя пользователя'),
            "service_user_url"      => t('user', 'Ссылка но соц. сеть'),
            "service_user_pic"      => t('user', 'Фото'),
            "service_user_email"    => t('user', 'Эл. почта'),
        );
    }

}