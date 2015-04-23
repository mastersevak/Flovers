<?php 


/**
* Модель UserBlock - где хранятся заблокированные пользователи
*
* @property integer     $id       - id
* @property integer     $user_id  - id пользователь
* @property string      $username - имя пользователь
* @property string      $ip       - ip
* @property date        $date     - дата
* 
*/
class UserBlock extends AR
{
	
	public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    // отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'user_block';
    }

}