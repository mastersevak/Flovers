<?php 

/**
 * SContentEditable 
 *
 * виджет для рисования contenteditable полей, с 
 * привязкой к скрипту обновления
 */
 class SContentEditable extends SWidget
 {
 	public $value;
 	public $attributes = [];
 	public $url;

 	public function run(){

		if(!isset($this->attributes['class'])) 
			$this->attributes['class'] = '';

		$this->attributes['class'] .= 'content-editable-input';
		$this->attributes['data-value'] = $this->value;
		$this->attributes['contenteditable'] = 'true';
		$this->attributes['data-url'] = !$this->url ? url('/core/ajax/contenteditable') : $this->url;

		if(!isset($this->attributes['data-model'])) 
			throw new Exception('Не указан параметр data-model для поля UIHelpers::contentEditable');
		if(!isset($this->attributes['data-attribute'])) 
			throw new Exception('Не указан параметр data-attribute для поля UIHelpers::contentEditable');
		if(!isset($this->attributes['data-searchvalue'])) 
			throw new Exception('Не указан параметр data-searchvalue для поля UIHelpers::contentEditable');

		echo CHtml::tag('span', $this->attributes, $this->value);
 	}
 	
 } 

/**
 * @todo 
 * Добавить фунцкионал
 *
 * 1. проблемы в firefox
 * 2. suffix и prefix
 *
 * afterchange
 * beforechange
 */