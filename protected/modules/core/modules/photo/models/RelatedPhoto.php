<?php 

/**
 * RelatedPhoto - модел для связки фотографий с моделями
 * @property integer    $id         - id шаблона
 * @property integer    $photo_id   - id фото
 * @property integer    $model_id   - id модель
 * @property string     $model      - модель
 * 
 */
 class RelatedPhoto extends AR
 {
 	
 	public static function model($classname = __CLASS__){
        return parent::model($classname);
    }

    // отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'related_photo';
    }

 	
 }

