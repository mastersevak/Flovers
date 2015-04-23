<?php

Yii::import('zii.widgets.grid.CCheckBoxColumn');

/**
* исправлено поведение uniform checkbox update
 */
class SCheckBoxColumn extends CCheckBoxColumn
{
	public $headerTemplate='{item}';
	
	public $id = 'checked_rows';

	public $headerHtmlOptions = array('width'=>30);

	public $selectableRows = 2;

	/**
	 * Initializes the column.
	 * This method registers necessary client script for the checkbox column.
	 */
	public function init()
	{

		parent::init();

		$this->id = $this->grid->id . '_' .$this->id;

		if(isset($this->checkBoxHtmlOptions['name']))
			$name=$this->checkBoxHtmlOptions['name'];
		else
		{
			$name=$this->id;
			if(substr($name,-2)!=='[]')
				$name.='[]';
			$this->checkBoxHtmlOptions['name']=$name;
		}
		$name=strtr($name,array('['=>"\\[",']'=>"\\]"));
		
			//.. process check/uncheck all
			$cball=<<<CBALL
jQuery(document).on('click', '.grid-view #{$this->id}_all',function() {
	var checked=this.checked;
	console.log('test');
	$(this).closest('table').find("tbody td.checkbox-column input:checkbox:enabled").each(function() {this.checked=checked;});
});

CBALL;
	
		cs()->registerScript($this->grid->id.'_check_all', $cball, CClientScript::POS_READY);
	}


	public function renderHeaderCell()
	{
		$this->headerHtmlOptions['class'] = actual($this->headerHtmlOptions['class'], '');
		if($this->grid->showHiddenColumns) $this->headerHtmlOptions['class'] .= ' has-hidden-elements';

		if(Cookie::get("hidden-column-{$this->id}")) { //для того чтобы скрытые колонки не рисовались
			$this->headerHtmlOptions['class'] .= " hidden";
		}
		
		parent::renderHeaderCell();
	}


	public function renderFilterCell()
	{
		if(Cookie::get("hidden-column-{$this->id}")) { //для того чтобы скрытые колонки не рисовались
			$this->filterHtmlOptions['class'] = actual($this->filterHtmlOptions['class'], '') . ' hidden';
		}
		
	   	parent::renderFilterCell();
	}

	/**
	 * Renders a data cell.
	 * @param integer $row the row number (zero-based)
	 */
	public function renderDataCell($row)
	{
		if(Cookie::get("hidden-column-{$this->id}")) { //для того чтобы скрытые колонки не рисовались
			$this->htmlOptions['class'] = actual($this->htmlOptions['class'], '') . ' hidden';
		}

		parent::renderDataCell($row);
	}

	//обертка
	protected function renderHeaderCellContent(){
		if($this->grid->enableSelectAll){
			echo CHtml::openTag('div', ['class'=>'checkbox check-default']);
				parent::renderHeaderCellContent();
				echo CHtml::tag('label', ['for'=>$this->id.'_all'], '');
			echo CHtml::closeTag('div');
		}
		
	}

	//обертка
	protected function renderDataCellContent($row, $data){
		echo CHtml::openTag('div', ['class'=>'checkbox check-default']);
			parent::renderDataCellContent($row, $data);
			echo CHtml::tag('label', ['for'=>$this->id.'_'.$row], '');
		echo CHtml::closeTag('div');
	}
	
}



