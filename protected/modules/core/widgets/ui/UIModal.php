<?php 

/**
* UIModal
*/
class UIModal extends SWidget
{
	public $width = 600;
	public $title;
	public $bodyClass = 'modal-body';
	public $form = true;
	public $footer = true;
	public $draggable = true;
	public $backdrop = true;
	public $languageSelector = false;

	public $footerButtons = [
		// 'close' => [ 'value' => 'Закрыть', 'icon' => false, 
		// 				'htmlOptions' => ['type'=>'button', 'class'=>'btn btn-default btn-small', 'data-dismiss'=>'modal']],
		'submit' => [ 'value' => 'Сохранить', 'icon' => false, 
						'htmlOptions' => ['type'=>'submit', 'class'=>'btn btn-success btn-small']]
	];
	
	public function init(){

		parent::init();

		$params = [
				'id' => $this->id,
				'class' => 'modal animated fadeInUp',
				'tabindex' => '1',
				'role' => 'domodal-dialog',
				'aria-hidden' => 'true'
			];

		if(!$this->backdrop) { 
			$params['data-backdrop'] = false;
			$params['style'] = 'width:'.$this->width.'px';
		}

		echo CHtml::openTag('div', $params);
			
			if($this->languageSelector) 
				app()->controller->widget('core.widgets.language.LanguageSelector', ['ajax' => true]);

			echo CHtml::openTag('div', ['class'=>'modal-content', 'style' => 'width:'.$this->width.'px']);

			//HEADER
			if($this->title){
				echo CHtml::openTag('div', ['class'=>'modal-header'.($this->draggable ? ' moveable' : '')]);

					echo CHtml::htmlButton('&times;', [
						'class'=>'close',
						'data-dismiss'=>'modal',
						'data-draggable' => true,
						'aria-hidden'=>'true']);
					echo CHtml::tag('h4', ['class'=>'modal-title'], 
							Yii::app()->format->custom('normal<semibold>', $this->title));

				echo CHtml::closeTag('div'); //modal-header
			}

			if(!$this->form)
				echo CHtml::openTag('div', ['class'=>$this->bodyClass]);
}

	public function run(){
				
				if(!$this->footer)
					echo CHtml::closeTag('div'); //body

			echo CHtml::closeTag('div'); //modal-content

		echo CHtml::closeTag('div'); //modal

		Yii::app()->clientScript->registerScriptFile($this->assetsUrl.'/js/uimodal.js');
	

		if($this->draggable){
			$draggableScript = "$('#{$this->id}')";
			if($this->backdrop) $draggableScript .= ".find('.modal-content')";
			$draggableScript .= ".draggable({handle: '.modal-header'});";

			cs()->registerScript('drag-modal'.$this->id, $draggableScript);
		}

		cs()->registerScript('domodalInit'.$this->id, "$('#{$this->id}').doModal();");

		return $this;
	}

	public function header(){
		echo CHtml::openTag('div', ['class'=>$this->bodyClass]);
	}

	public function footer($buttons = [], $replace = false, $htmlOptions = []){
		echo CHtml::closeTag('div'); //body

		if(isset($htmlOptions['class'])) 
			$htmlOptions['class'] .= ' modal-footer';
		else $htmlOptions['class'] = 'modal-footer';

		echo CHtml::openTag('div', $htmlOptions);
		
		if($buttons){
			if($replace)
				$this->footerButtons = $buttons;
			else
				$this->footerButtons = CMap::mergeArray($this->footerButtons, $buttons);
		}

		foreach($this->footerButtons as $button){
			$value = $button['value'];
			if($button['icon'])
				$value = CHtml::tag('i', ['class'=>$button['icon']], '') . $value;

			echo CHtml::tag('button', $button['htmlOptions'], $value );
		}

		echo CHtml::closeTag('div');
	}
}