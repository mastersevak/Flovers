<?php 


/**
* UITabs Widget
*
* Пример использования
*
* // аякс
* {begin_widget name='UITabs' tabs=['tab1'=>'My Test tab', 'tab2'=>'My test tab 2']}
* 	<div class="tab-pane active" id="tab1">Content 1</div>
*  	<div class="tab-pane" id="tab2">Content 2</div>
* {/begin_widget}
* 
* //не аякс
* {begin_widget name='UITabs' paramName='type' tabs=['tab1'=>'My Test tab', 'tab2'=>'My test tab 2'] ajax=false}
* 	Content
* {/begin_widget}
*/
class UITabs extends SWidget
{
	public $tabs = array();
	public $ajax = true;
	public $settings = false; //кнопка настроек

	public $paramName = 'param';
	/**
	 * Initializes the widget.
	 * This renders the start of tabs
	 */
	public function init(){
		parent::init();

		$items = [];
		
		$active = true;
		//draw the tabs
		
		foreach($this->tabs as $key=>$tab) {
			
			if(is_array($tab)){
				$items[] = $tab;
			}
			else{
				$items[] = [
					'label' => $tab,
					'url' => $this->ajax ? '#'.$key : [app()->controller->action->id, $this->paramName=>$key],
					'active' => $this->ajax && $active || 
								(!request()->getParam($this->paramName) && $active) || 
									(request()->getParam($this->paramName) == $key),
				];
			}

			$active = false;
		}

		if($this->settings)
			$items[] = [
				'label' => CHtml::tag('div', ['class'=>'tools'], 
							CHtml::htmlButton('', [
								'class'=>'config', 
								'data-toggle'=>'modal',
								'data-target'=>'#settings-modal',
								'rel' => 'tooltip',
								'title' => t('admin', 'Настройки')])),
				'itemOptions' => ['style'=>'float:right']
			];

		$this->widget('SMenu', [
				'id' => $this->id,
				'encodeLabel' => false,
				'htmlOptions' => ['class'=>'nav nav-tabs'],
				'items' => $items
			]);
	}

	/**
	 * Initializes the widget.
	 * This renders the end of tabs
	 */
	
	public function run(){
		if($this->ajax)
			Yii::app()->clientScript->registerScript('run_tab_'.$this->id, 
				"$('#{$this->id}').on('click', 'a', function(e){e.preventDefault(); $(this).tab('show');});
				$('a[href='+window.location.hash+']').tab('show');");
	}
}