<?php

Yii::import('zii.widgets.grid.CDataColumn');

/**
* для показа в середине колонок с птичкой
 */
class SBooleanColumn extends SDataColumn
{
	public $headerHtmlOptions = array('width'=>30);
	public $type = 'raw';
	public $color;
	public $title;

	public $checkboxType = 'normal';

	public $evaluateHtmlOptions = false;

	public $editable;

	private $_colors = [
		'red' => 'check-danger',
		'green' => 'check-primary',
		'blue' => 'check-success',
		'yellow' => 'check-info',
		'temp' => 'check-warning'
	];

	private $_checkboxTypes = [
		'normal' => 'checkbox',
		'star' => 'star',
		'circle' => 'checkbox checkbox-circle'
	];

	public function init(){
		parent::init();

		$this->htmlOptions = CMap::mergeArray([
			'class'=>'boolean-column'.($this->editable ? ' editable' : ''), 
			'data-attribute'=>$this->name,
			'align' => 'center'], $this->htmlOptions);
	}

	/**
	 * Renders the data cell content.
	 * This method evaluates {@link value} or {@link name} and renders the result.
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	protected function renderDataCellContent($row, $data)
	{ 
		if($this->value!==null)
			$value=$this->evaluateExpression($this->value, array('data'=>$data, 'row'=>$row));
		elseif($this->name!==null)
			$value=CHtml::value($data, $this->name);
		
		$modelName = get_class($this->grid->dataProvider->model);
		$name = "{$modelName}[checkbox_{$this->name}_$data->primaryKey]";
		$id = "{$modelName}_checkbox_{$this->name}_$data->primaryKey";
		
		$divClass = isset($this->_checkboxTypes[$this->checkboxType]) ? $this->_checkboxTypes[$this->checkboxType] : $this->_checkboxTypes['normal'];
		//add color
		if($this->color && isset($this->_colors[$this->color]))
			$divClass .= ' '. $this->_colors[$this->color];
		
		//editable
		if($this->editable!= null && !is_bool($this->editable))
			$params['disabled'] = !$this->evaluateExpression($this->editable, array('data'=>$data, 'row'=>$row));

		$params['class'] = $divClass;
		if(isset($this->htmlOptions['class']))
			$params['class'] .= ' '.$this->htmlOptions['class'];

		if($this->title!=null){
			$title = $this->evaluateExpression($this->title, array('data'=>$data, 'row'=>$row));

			$params += ['rel' => 'tooltip', 'title' => $title];
		}

		echo CHtml::openTag('div', $params);
		echo CHtml::checkbox($name, $value, $params);
		echo CHtml::label('', $id);
		echo CHtml::closeTag('div');
	}	

	/*public function renderDataCell($row)
    {
        $data=$this->grid->dataProvider->data[$row];
        $this->htmlOptions['data-id'] = $data->id;
        parent::renderDataCell($row);
    }*/
}