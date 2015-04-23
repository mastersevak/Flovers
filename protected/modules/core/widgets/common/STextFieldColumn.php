<?php

class STextFieldColumn extends SDataColumn
{
	public $textFieldOptions = [];
	public $filterOptions = [];

	public function init(){

		$this->headerHtmlOptions = CMap::mergeArray(['width'=>50], $this->headerHtmlOptions);

		$this->filterOptions = CMap::mergeArray(['id'=>false,'prompt'=>'', 'align'=>'right'], $this->filterOptions);
	}
	

	public function renderDataCellContent($row, $data)
	{
		$data = CMap::mergeArray(array(
			'{name}'  => "{$this->name}[{$data->id}]",
			'{value}' => $data->{$this->name},
			'{class}' => $this->name,
			'{style}' => "width: 100%"
		), $this->textFieldOptions);

		$options = '';

		foreach($data as $key=>$value){	
			$options .= substr($key, 1, -1) . '="' . $key . '" ';
		}

		echo strtr('<input type="text" '.$options.'>', $data);
	}

	protected function renderFilterCellContent()
	{
		if(is_string($this->filter))
			echo $this->filter;
		elseif($this->filter!==false && $this->grid->filter!==null && $this->name!==null && strpos($this->name,'.')===false)
		{
			if(is_array($this->filter))
				echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, $this->filterOptions);
			elseif($this->filter===null)
				echo CHtml::activeTextField($this->grid->filter, $this->name, $this->filterOptions);
		}
		else
			parent::renderFilterCellContent();
	}
}