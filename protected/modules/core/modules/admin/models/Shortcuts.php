<?php

/**
 * @property integer    $id         - id шаблона
 * @property integer    $id_object  - 
 * @property integer    $shortcode  -
 * @property integer    $pos      	- позиция
 * 
 */

class Shortcuts extends AR
{

	const SHORTCODE_HOMEARTICLE = 1;

	public function init(){
		parent::init();
	}

	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	// отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'shortcut';
    }


	public function relations()
	{

		return array(
			'news' => array(self::BELONGS_TO, 'News', 'id_object'),
			'article' => array(self::BELONGS_TO, 'Article', 'id_object')
		);
	}


	public function scopes(){
		return array(
			'homenews' => array(
				'condition' => 'shortcode = :news or shortcode =:article',
				'params'	=> array(':news' => self::SHORTCODE_HOMENEWS, ':article'=>self::SHORTCODE_HOMEARTICLE),
			),
		);
	}

	public function behaviors(){
	    return CMap::mergeArray(parent::behaviors(), array(
	        'sortable' => array(
	            'class' => 'SortableBehavior',
	        ),
	    ));
	}

}