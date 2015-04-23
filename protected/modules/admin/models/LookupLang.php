<?php

class LookupLang extends MlLang
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CategoryLang the static model class
	 * 
	 * @property integer    $id         - id шаблона
	 * @property string   	$language 	- язык
	 * @property integer 	$lookup_id 	-
	 * @property string 	$name       - имя
	 */

	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	// отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'lookup_lang';
    }

	

}