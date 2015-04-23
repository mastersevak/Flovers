<?php
/**
 * @return Message
 * 
 * @property integer    $id     	- id шаблона
 * @property string     $category 	- категория
 * @property string   	$language 	- язык
 * @property string   	$message 	- сообщение
 * @property string   	$page 		- страница
 * 
 */
class MissingTranslation extends AR
{
	
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
         return 'missing_translations';
    }
	

}