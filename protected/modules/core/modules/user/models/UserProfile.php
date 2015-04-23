<?php
/**
 * This is the model class for table "{{user_profile}}".
 * Профиль пользователя с дополнительными данными
 *
 *
 * в текущий момент не используется
 */

class UserProfile extends AR
{        
    const GENDER_MALE   = 1;
    const GENDER_FEMALE = 2;
     
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    // отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'user_profile';
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
            array('firstname, lastname, middlename, about, address, phone', 'filter', 'filter'=>'trim'),
            //filter purify
            array('firstname, lastname, middlename, about, address, phone',
                    'filter', 'filter' => [$this->purifier, 'purify']),
            
            //type
            array('country', 'numerical', 'integerOnly'=>true),
            array('birthday', 'date', 'format'=>'dd/MM/yyyy', 'on' => 'update'),

            //max, min
            array('firstname, lastname, middlename', 'length', 'max'=>50),

            /**
             * Правила для update
             */

            //range
            array('gender', 'in', 'range' => array_keys(Lookup::items('UserGender')), 
                    'on'=>'update'),

            /**
             * Правила для registration
             */

            //required
            array('firstname', 'required', 'on'=>'registration, fregistration'),
            
            //range
            array('gender', 'in', 'range' => array_keys(Lookup::items('UserGender')), 
                    'on'=>'register'),
        );
    }

    //только для данного проекта
    public function relations(){
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id')
        );
    }

    public function behaviors(){
        return CMap::mergeArray(parent::behaviors(), array(
            'dateBehavior' => array(
                'class'           => 'DateBehavior',
                'dateAttribute'   => 'birthday'
            ),
        ));
    }

    /**
     * Функция которая возвращает масив с названиями labels для соответствующих полей
     */
    public function attributeLabels() {
        
        return array(
            'firstname'     =>  t('user', 'Имя'),
            'middlename'    =>  t('user', 'Отчество'),
            'lastname'      =>  t('user', 'Фамилия'),
            'gender'        =>  t('user', 'Пол'),
            'birthday'      =>  t('user', 'Дата рождения'),
            'about'         =>  t('user', 'О себе'),
            'country'       =>  t('user', 'Страна'),
            'city'          =>  t('user', 'Город'),
            'phone'         =>  t('user', 'Телефон'),
            'address'       =>  t('user', 'Адрес'),
            'about'         =>  t('user', 'Заметки')
        );
    }

}