<?php 

Yii::import('zii.widgets.grid.CDataColumn');

/**
* SDataColumn
*/
class SDataColumn extends CDataColumn
{
	public $enableHideColumn = true;
	public $filterHtmlOptions = [];

	public function renderHeaderCell()
	{
		$this->headerHtmlOptions['class'] = actual($this->headerHtmlOptions['class'], '');
		if($this->grid->showHiddenColumns && $this->enableHideColumn) $this->headerHtmlOptions['class'] .= ' has-hidden-elements';

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
	
	protected function renderFilterCellContent()
	{

		if(is_string($this->filter))
			echo $this->filter;
		elseif($this->filter!==false && $this->grid->filter!==null && $this->name!==null && strpos($this->name,'.')===false)
		{
			if(is_array($this->filter))
				echo UIHelpers::dropDownList($this->grid->filter, $this->name, $this->filter, 
					CMap::mergeArray([
						'id' => false,
						'prompt' => '',
						'data-width' => '100%'
					], $this->filterHtmlOptions));
			elseif($this->filter===null)
				echo CHtml::activeTextField($this->grid->filter, $this->name, CMap::mergeArray($this->filterHtmlOptions, ['id' => false]));
		}
		else
			parent::renderFilterCellContent();
	}


	protected function renderHeaderCellContent()
	{
		parent::renderHeaderCellContent();
		if($this->grid->showHiddenColumns && $this->enableHideColumn)
			echo CHtml::link("", "#", ['class'=>'hide-column fa fa-minus-square-o']);
	}
}