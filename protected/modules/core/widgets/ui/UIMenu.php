<?php 

/**
* UIMenu
*
* виджет для создания меню
* 
* Использование 
* 
* $this->widget('UIMenu', ['buttons'=>['http://...'=>'Название кнопки', ...])
*/
class UIMenu extends SWidget
{
	public $buttons = [];
	public $id;
	
	public function run(){

		$result = "";

		foreach($this->buttons as $button){
			if(is_array($button['url'])) {
				$params = (count($button['url']) > 1) ? array_slice($button['url'], 0, 1) : array();
				$url = Yii::app()->createUrl($button['url'][0], $params);
			}
			$active = isset($button['active']) ? $button['active'] : false;
			$result .= $this->getButton($button['name'], $url, $active);
		}

		Yii::app()->clientScript->registerScriptFile($this->assetsUrl.'/js/ui.js');

		echo $result;
	}

	public function getButton($title, $url, $active){
		
		$options = [
			'class'=>'btn active' . (request()->url == $url || $active ? ' btn-primary' : ''),
			'type'=>'button',
			'data-url'=>$url,
			'onclick'=>'UIMenu.buttonClick(this)'];

		return CHtml::htmlButton(CHtml::encode($title), $options);
	}

}