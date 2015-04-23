<?php

/**
 * This is the model class for table "{{photo_lang}}".
 *
 * The followings are the available columns in table '{{photo_lang}}':
 * @property string     $id
 * @property integer    $photo_id
 * @property string     $language
 * @property string     $title
 */
class PhotoLang extends MlLang
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
         return 'photo_lang';
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