<?php 

/** 
 * Для создания производного класса
 * 1. нужно определить ml behavrior
 * пример: 
 * 
 'ml' => [
    'class' => 'core.behaviors.Ml',
    'langTableName' => 'page_lang',
    'langForeignKey' => 'id_page',
    'localizedAttributes' => array('title', 'content', 'meta_title', 'meta_keywords', 'meta_description'), //attributes of the model to be translated
]
 * 2. нужно определить сами переменные которые должны быть 
 *    мультиязычными.
 * 3. Создать для этих полей правила валидации 
 * 4. И наконец создать таблицу с мултиязычными полями, в которой
 *    должны присутствовать двя объязательных поля: language, classname_id
 * 5. И соответственно 2ая таблица должна заканчиваться на lang
 */

Yii::import('core.behaviors.MultilangualBehavior');

 class Ml extends MultilangualBehavior
 {

    public $langField = 'language';
    public $localizedPrefix = '';
    public $createScenario = 'create';
    public $dynamicLangClass = true;

    public function __construct(){

        $this->languages = Yii::app()->params['languages']; // array of your translated languages. Example : array('fr' => 'Français', 'en' => 'English')
        $this->defaultLanguage = Yii::app()->params['defaultLanguage']; //your main language. Example : 'fr']
    }
 }

 ?>