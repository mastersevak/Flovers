<?php

class SAutocompleteColumn extends SDataColumn
{
    public $headerHtmlOptions = [];
    public $editable = false;
    public $autocompleteModel;

    public function init(){
        parent::init();

        if(!$this->filter){
            $model = $this->grid->filter;
            if($model){
                $filter_id = get_class($model)."_".$this->name;

                $this->filter = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'model'=>$model,
                            'attribute'=>$this->name,
                            'source' => Yii::app()->createUrl('/core/ajax/autocomplete?model='.$this->autocompleteModel),
                        ), true);

                $this->grid->afterAjaxUpdateFunctions[] =  "$('#{$filter_id}').autocomplete({'source':'/core/ajax/autocomplete?model={$this->autocompleteModel}'});";
            }
        }

        if($this->editable){
            $this->htmlOptions = CMap::mergeArray([
                    'class'=>'contenteditable autocomplete', 
                    'data-attribute'=>$this->name
                ], $this->htmlOptions);

            
        }
    }

}

?>

