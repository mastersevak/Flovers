<?php

class SUserColumn extends SDataColumn
{

    /**
     * @var boolean whether the htmlOptions values should be evaluated. 
     */
    public $evaluateHtmlOptions = true;
 

    public function init(){
        parent::init();

        $this->htmlOptions = CMap::mergeArray([
                'class'=>'user', 
                'data-attribute'=>$this->name
            ], $this->htmlOptions);
    }

     /**
     * Renders a data cell.
     * @param integer $row the row number (zero-based)
     * Overrides the method 'renderDataCell()' of the abstract class CGridColumn
     */
    public function renderDataCell($row)
    {
            $data=$this->grid->dataProvider->data[$row];

            $this->htmlOptions['data-user-id'] = user()->id;
            
            parent::renderDataCell($row);
    }

    /**
     * Renders the data cell content.
     * This method evaluates {@link value} or {@link name} and renders the result.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row, $data)
    {
        
        $class = $data->date_operation < $data->date_changed ? 'changed' : '';

        echo CHtml::link(($data->user ? $data->user->fullName : ''), '#', 
                            ['rel'=>'tooltip', 'class' => $class,
                             'data-original-title'=>"Обновлен: {$data->date_changed}"]);
    }

}

?>

