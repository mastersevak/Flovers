<?php

/**
 * PhotoalbumLang model
 *
 * @property integer $id            	- id
 * @property integer $photoalbum_id 	- 
 * @property string  $language      	- язык
 * @property string  $title         	- название
 */

class PhotoalbumLang extends MlLang
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PageContent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	// отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'photoalbum_lang';
    }


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		
		return array(
			array('title', 'filter', 'filter'=>'trim'),
			array('title', 'filter', 'filter' => [$this->purifier, 'purify'] ),
			array('title', 'length', 'max'=>255),
		);
	}

}