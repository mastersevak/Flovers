<?php 

/**
* SMultilangColumn 
*
* колонка, для показа мультиязычного поля
*/
class SMultilangColumn extends SDataColumn
{
	public $type = 'raw';
	/**
	 * Renders the data cell content.
	 * This method evaluates {@link value} or {@link name} and renders the result.
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	protected function renderDataCellContent($row,$data)
	{
		if($this->value!==null)
			$value=$this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
		elseif($this->name!==null){
			//тут переделываем для всех языков
			$value = '';
			$langs = param('languages');
			$defLang = param('defaultLanguage');

			foreach($langs as $key => $lang){
				$value .= CHtml::tag('span', ['class' => 'multilang '.$key. ($key == $defLang ? '' : ' hidden')], 
					CHtml::value($data, $this->name."_$key"));
			}
			
		}
		echo $value===null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value,$this->type);
	}
}