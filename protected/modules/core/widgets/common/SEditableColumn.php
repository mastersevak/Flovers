<?php

class SEditableColumn extends SDataColumn
{

    /**
     * @var boolean whether the htmlOptions values should be evaluated. 
     */
    public $evaluateHtmlOptions = true;

    public function init(){
        parent::init();

        $this->htmlOptions = CMap::mergeArray([
                'class'=> 'contenteditable', 
                'data-attribute'=>$this->name
            ], $this->htmlOptions);
    }


    public function renderHeaderCell()
    {
        if($this->grid->autoIncrement){
            $model = get_class($this->grid->dataProvider->model);;
            $this->headerHtmlOptions['data-autoincrement'] = CHtml::textField("{$model}[{$this->name}]");  
        }
        
        parent::renderHeaderCell();
    }

}

?>

