<?php 

/**
* SHiddenColumn
*/
class SHiddenColumn extends SDataColumn
{
	public $enableHideColumn = true;

	public function renderHeaderCell()
	{
		$this->headerHtmlOptions['class'] = 'hidden';
		parent::renderHeaderCell();
	}


	public function renderFilterCell()
	{
		$this->filterHtmlOptions['class'] = 'hidden';
	   	parent::renderFilterCell();
	}

	/**
	 * Renders a data cell.
	 * @param integer $row the row number (zero-based)
	 */
	public function renderDataCell($row)
	{
		$this->htmlOptions['class'] = 'hidden';
		parent::renderDataCell($row);
	}

	/**
	 * Renders a footer cell.
	 * @param integer $row the row number (zero-based)
	 */
	public function renderFooterCell()
	{
		$this->footerHtmlOptions['class'] = 'hidden';
		parent::renderFooterCell();
	}
}