<?php 

/**
* UIFilters
*
* виджет для работы, с блоками фильтров
* 
* Использование 
* 
* //для предопределленных блоков
* $this->widget('UIFilters', ['blocks'=>'update']) //group values (index, update, create, category ...)
* 
*/
class UIFilters extends SWidget
{

	public $blocks;
	public $model; 
	public $ajaxUpdate = false;
	public $class = '';
	public $gridId; //для необъязательной связки grid-а с формой поиска

	public $blocksIds = []; //айдишники блоков
	public static $_counter = 0;

	public $beforeSearch = "Filters.beforeSearch";
	public $onSearch = "Filters.onSearch";

	public $viewPath = 'app.views';

	public function run(){

		$filters = "";
		$titleArr = [];

		foreach($this->blocks as $title => $block){

			$model = $this->model ? $this->model->filters : false;
			$blockId = $this->getId();
			$blocksIds[] = $blockId;

			$hiddenBlock = Cookie::get("hidden-fb-".$blockId) ? 'hidden' : '';


			if(is_array($block)){
				if(isset($block['name'])){
					
					if(isset($block['hidden'])){
						foreach($block['hidden'] as $hidden)
							$model->hiddenFields[] = $hidden;
					}
					$titleArr[] = $title;

					$height = isset($block['height']) ? $block['height'] : false;
					$params = isset($block['params']) ? $block['params'] : false;

					$_block = $this->render($this->viewPath.'.uifilters.blocks.'.$block['name'], ['model'=>$model, 'height'=>$height, 'params' => $params], true);
					
					$fill = isset($block['fill']); //используется для возможности toggle reset на заглавии
					$fillExceptions = $this->fillExceptions($block); //используется для возможности toggle reset на заглавии, если нужно указать исключения

					$filters .= $this->render('core.widgets.ui.views.uifilters._block', 
						['content'=>$_block, 'title'=>$title, 'fill'=>$fill, 'fillExceptions'=> $fillExceptions, 'id'=>$blockId, 'hidden'=>$hiddenBlock, 'name'=>$block['name'], 'float' => ''], true);
				}
				else{
					$filters .= CHtml::openTag('div', ['class' => 'fl'], '');
					foreach($block as $_title => $one){
						$height = isset($one['height']) ? $one['height'] : false;
						$params = isset($one['params']) ? $one['params'] : false;

						$titleArr[] = $_title;
						
						$_block = $this->render($this->viewPath.'.uifilters.blocks.'.$one['name'], ['model'=>$model, 'height'=>$height, 'params' => $params], true);
						
						$fill = isset($one['fill']); //используется для возможности toggle reset на заглавии
						$fillExceptions = $this->fillExceptions($one); //используется для возможности toggle reset на заглавии, если нужно указать исключения

						$filters .= $this->render('core.widgets.ui.views.uifilters._block', 
							['content'=>$_block, 'title'=>$_title, 'fill'=>$fill, 'id'=>$blockId, 'fillExceptions'=> $fillExceptions, 'hidden'=>$hiddenBlock, 'name'=>$one['name'], 'float' => 'no-float'], true);
					}
					$filters .= CHtml::closeTag('div');	
				}
				
			}
			else{
				$titleArr[] = $title;

				$_block = $this->render($this->viewPath.'.uifilters.blocks.'.$block, ['model'=>$model], true);
				$filters .= $this->render('core.widgets.ui.views.uifilters._block', 
					['content'=>$_block, 'title'=>$title, 'id'=>$blockId, 'hidden'=>$hiddenBlock, 'name'=>$block, 'float' => ''], true);
			}
		}

		$this->blocksIds = array_combine($blocksIds, $titleArr);

		cs()->registerScriptFile($this->assetsUrl .'/js/filters.js');
		
		$htmlOptions = ["class" => "search-form main"];

		if($this->gridId) $htmlOptions['data-grid-id'] = $this->gridId;

		$this->class .= (Cookie::get("hiddenTopFilters".get_class($this->model)) ? ' compact' : '');

		$this->beginWidget('SActiveForm', [
			'htmlOptions' => $htmlOptions,
			'clientOptions' => ['validateOnSubmit' => false]
			]);
		
			$this->render('core.widgets.ui.views.uifilters._main', ['content'=>$filters, 'ajaxUpdate'=>$this->ajaxUpdate]);
		
		$this->endWidget();

		cs()->registerPackage('inputstyler');

		cs()->registerScript("top-filters-input-styler", 
			"$('#top-filters input[data-filter]').inputStyler();
			$('#top-filters').closest('form').get(0).beforesearch = {$this->beforeSearch};
			$('#top-filters').closest('form').get(0).onsearch = {$this->onSearch};");
	}

	public function getId($autoGenerate=true){
		return 'filter_block_'.self::$_counter++;
	}


	public static function filtered($model){
		return ($model->getIsFiltered() ? 'filtered' : '');
	}

	public static function isHidden(){

	}

	/**
	 * Рисует список скрытых блоков
	 */
	public function renderHiddenBlocks(){
		foreach ($this->blocksIds as $id => $title) {
			if(Cookie::get("hidden-fb-{$id}")){
				echo CHtml::openTag('li');
					echo CHtml::openTag('div', ['class' => "checkbox"]);
						echo CHtml::checkbox("show_hfb_{$id}");
						echo CHtml::label($title, "show_hfb_{$id}");
					echo CHtml::closeTag('div');
				echo CHtml::closeTag('li');
			}
		}
	}


	private function fillExceptions($block){
		$fillExceptions = '';

		if(isset($block['fill-exceptions'])){
			foreach ($block['fill-exceptions'] as &$exception) {
				$exception = $block['className'].'_'.$exception;	
			}
			$fillExceptions = implode(',', $block['fill-exceptions']);
		}

		return $fillExceptions;
	}


	public function renderField($field, $model = false, $name = false, $params = false){
		if(!$name) $name = $field;

		$this->render($this->viewPath.".uifilters.fields.{$field}", compact('name', 'model', 'params'));
	}

}
