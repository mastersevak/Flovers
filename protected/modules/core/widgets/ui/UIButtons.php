<?php 

/**
* UIButtons
*
* виджет для работы, кнопками
* 
* Использование 
* 
* //для предопределленных групп кнопок
* $this->widget('UIButtons', ['group'=>'update']) //group values (index, update, create, category ...)
* 
* $this->widget('UIButtons', [
* 		'buttons' => ['Create', 'Delete', 'ShowImages' => ['class'=>'btn-show-images' ... other html options]
* 					]])
*/
class UIButtons extends SWidget
{
	public $groups = [
		'index'		=> ['Search', 'Create', 'DeleteSelected', 'ShowImages', 'ClearFilters'],
		'update'	=> ['Save', 'SaveAndClose',/* 'Delete',*/ 'Close'],
		'create'	=> ['Save', 'SaveAndClose', 'Close'],
		'category'	=> ['Create', 'Save', 'Delete'],
		'save'		=> ['Save']
	];

	public $buttons = [];
	public $button = [];
	public $group;
	public $size = ''; //размер кнопки
	public $form; //id формы для которого будет работать submit

	public $id;
	
	public function run(){

		$result = "";

		if($this->group && isset($this->groups[$this->group])){
			foreach($this->groups[$this->group] as $button){
				if(method_exists($this, 'get'.$button))
					$result .= call_user_func(array($this, 'get'.$button));
			}
		}

		foreach($this->buttons as $key => $button){
			if(is_string($button)){
				if(method_exists($this, 'get'.$button))
					$result .= call_user_func(array($this, 'get'.$button));
			}

			if(is_array($button)){
				if(!isset($button['visible']) || $button['visible'])
					if(method_exists($this, 'get'.$key))
						$result .= call_user_func(array($this, 'get'.$key), $button);
					else
						$result .= $this->getCustom($button);
			}
		}

		Yii::app()->clientScript->registerScriptFile($this->assetsUrl.'/js/ui.js');

		echo $result;
	}

	//размер кнопок
	private function size(){
		
		switch($this->size){
			case 'large':
			case 'small':
			case 'mini':
				return ' btn-'.$this->size;
			default:
				return 'btn-small';
		}

		return '';
	}

	/**
	 * КНОПКИ
	 * -------------------
	 */

	// empty button
	protected function getCustom($options = []){
		$value	 = actual($options['value'], '');
		$icon	 = actual($options['icon'], '');
		$ajax 	 = actual($options['ajax'], false);
		$options = actual($options['options'], []);

		$options = CMap::mergeArray(['class'=>'btn btn-cons'.$this->size()], $options);
		$label = CHtml::tag('i', ['class' => $icon], '').$value;
		
		if($ajax) return CHtml::ajaxButton($label, $ajax['url'], $ajax, $options);
		else return CHtml::htmlButton($label, $options);
	}

	//файл
	protected function getFile($options = []){
		$id   = actual($options['id'], '');
		$name = actual($options['name'], '');
		$value = actual($options['value'], 'Выбрать');

		unset($options['id'], $options['name'], $options['value']);

		$options = CMap::mergeArray([
			'class'	    => 'file-field',
			'btn-class' => 'btn btn-cons btn-white'.$this->size(),
			'onclick'   => 'UIButtons.file(this)'], 
			$options);

		$file  = CHtml::fileField($name, '', ['class'=> 'hidden '.$options['class'], 'id'=>$id]);
		
		$options['class'] = $options['btn-class'];
		unset($options['btn-class']);
		$file .= CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-cloud-upload'], '').$value, $options);
		
		return CHtml::tag('div', ['class'=>'ui-file'], $file);
	}

	//добавитъ
	protected function getAdd($options = []){
		$options = CMap::mergeArray(['class'=>'btn btn-cons btn-success '.$this->size()], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-plus'], '').t('admin', 'Добавить'), $options);
	}

	//сохранить
	protected function getSave($options = []){
		$options = CMap::mergeArray([
			'class'   => 'btn btn-cons btn-success '.$this->size(),
			'type'    => 'submit',
			'onclick' => 'UIButtons.save(this); return false;'], 
			$options);

		if($this->form) {
			$options['data-form'] = $this->form;
		}

		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-save'], '').t('admin', 'Сохранить'), $options);
	}

	//сохранить и закрыть
	protected function getSaveAndClose($options = []){
		$options = CMap::mergeArray([
			'class'        => 'btn btn-cons btn-primary '.$this->size(),
			'data-success' => 'Сохранить и закрыть',
			'type'         => 'submit',
			'onclick'	   => 'UIButtons.saveAndClose(this)'], 
			$options);

		if($this->form) $options['data-form'] = $this->form;

		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-share'], '').t('admin', 'Сохранить и закрыть'), $options);
	}

	//закрыть
	protected function getClose($options = []){
		$options = CMap::mergeArray([
			'class'    => 'btn btn-cons '.$this->size(),
			'data-url' => Yii::app()->user->getGridIndex(),
			'onclick'  => 'UIButtons.close(this)'], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-times'], '').t('admin', 'Закрыть'), $options);
	}

	//расширенный поиск
	protected function getSearch($options = []){
		$options = CMap::mergeArray([
			'class'       => 'btn btn-cons btn-primary '.$this->size(),
			'data-toggle' => "domodal", 
			'data-target' => "#search-modal"], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-search'], '').t('admin', 'Поиск'), $options);
	}

	//создать
	protected function getCreate($options = []){
		$options = CMap::mergeArray([
			'class'    => 'btn btn-cons btn-success '.$this->size(),
			'onclick'  => 'UIButtons.gotoUrl(this)',
			'data-url' => Yii::app()->controller->createUrl('create')], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-pencil'], '').t('admin', 'Создать'), $options);
	}

	//удалитъ
	protected function getDelete($options = []){
		$options = CMap::mergeArray([
			'class'    => 'btn btn-cons btn-danger '.$this->size(),
			'onclick'  => 'UIButtons.delete(this)',
			'data-url' => Yii::app()->controller->createUrl('delete', ['id'=>$this->id])], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-trash-o'], '').t('admin', 'Удалитъ'), $options);
	}

	//удалить выбранные
	protected function getDeleteSelected($options = []){
		$params = [];
		if( isset($options['model']) )
			$params['model'] = $options['model'];

		$options = CMap::mergeArray([
			'class'    => 'btn btn-cons btn-danger '.$this->size(),
			'data-url' => Yii::app()->controller->createUrl('deleteselected', $params),
			'onclick'  => 'UIButtons.deleteSelected(this)'], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-trash-o'], '').t('admin', 'Удалить выбранные'), $options);
	}

	//очистить фильтр
	protected function getClearFilters($options = []){
		$options = CMap::mergeArray([
			'class'   => 'btn btn-cons btn-info '.$this->size(),
			'onclick' => 'UIButtons.clearFilters(this)'], 
			$options);
		return CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-eraser'], '').t('admin', 'Очистить фильтр'), $options);
	}

	//показать скрыть картинки
	protected function getShowImages($options = []){
		$show = Cookie::get("show_images");
		$iconShow = CHtml::tag('i', ['class'=>'fa fa-eye'], '');
		$iconHide = CHtml::tag('i', ['class'=>'fa fa-eye-slash'], '');

		$options = CMap::mergeArray([
				'class'           => 'btn btn-cons '.$this->size(), 
				'onclick'         => 'UIButtons.showImages(this);',
				'data-show-title' => $iconShow.t('admin', 'Показать картинки'),
				'data-hide-title' => $iconHide.t('admin', 'Скрыть картинки')], 
			$options);
		return CHtml::htmlButton(($show ? $iconHide.t('admin', 'Скрыть картинки') : $iconShow.t('admin', 'Показать картинки')), $options);
	}

}