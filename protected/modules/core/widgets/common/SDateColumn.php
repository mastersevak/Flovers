<?php

class SDateColumn extends SDataColumn
{
    public $headerHtmlOptions = array('width'=>120);
    public $htmlOptions = array('style'=>'text-align:center');
    public $pluginOptions = [];
    public $format = "dd/mm/yyyy";
    public $rangeFilter = true;
    public $editable = false;

    public function init(){
        parent::init();

        if(!$this->filter){
            $model = $this->grid->filter;

            if($model){
                $pluginOptions = CMap::mergeArray([
                    'format' => $this->rangeFilter ? strtoupper($this->format) : strtolower($this->format) ,
                    'language' => 'ru'
                ], $this->pluginOptions);

                $this->filter = call_user_func(['UIHelpers', $this->rangeFilter ? 'dateRangeFilter' : 'dateFilter'], $model, $this->name, [
                    'pluginOptions' => $pluginOptions]);

                $jsfunc = $this->rangeFilter ? 'daterangepicker' : 'datepicker';
                $jsOptions = $this->rangeFilter ? CMap::mergeArray(UIHelpers::$dateRangeFilterOptions, $pluginOptions) : $pluginOptions;
                $jsOptions = CJavaScript::encode($jsOptions);

                $this->grid->afterAjaxUpdateFunctions[] = "$('#{$this->grid->id} .datepicker-filter input').{$jsfunc}({$jsOptions});";
            }
            
        }

        if($this->editable){
            $this->htmlOptions = CMap::mergeArray([
                    'class'=>'contenteditable date', 
                    'data-attribute'=>$this->name
                ], $this->htmlOptions);

            $this->attachBehavior('ywplugin', array('class' => 'yiiwheels.behaviors.WhPlugin'));
            $au = $this->getAssetsUrl(Yii::getPathOfAlias('yiiwheels.widgets.datepicker.assets'));
            cs()->registerScriptFile($au . '/js/bootstrap-datepicker.js');
            cs()->registerScriptFile($au . '/js/locales/bootstrap-datepicker.ru.js');
            cs()->registerCssFile($au . '/css/datepicker.css');

            cs()->registerScript('datecol_datepicker', "
                    if($('#datecol_init_datepicker').length == 0){
                        var input = $('<input type=\"text\" id=\"datecol_init_datepicker\">');
                        input.appendTo($('body'));
                        input.bdatepicker({format:'dd/mm/yyyy', language: 'ru', autoclose: true});
                    }
                ");
        }
    }

    protected function renderFilterCellContent()
    {
        echo CHtml::openTag('div', ['class'=>'datepicker-filter']);
        parent::renderFilterCellContent();
        echo CHtml::closeTag('div');
    }

}

?>

