<?php

class SortableColumn extends CDataColumn
{

    public $name = ''; // 'sort'
    public $value = '';
    public $filter = false;

    public $afterAjaxUpdate = null;

    public $headerHtmlOptions = array('width'=>30);


    public function init()
    {
        $this->registerScripts();

        parent::init();
    }


    public function registerScripts()
    {
        $name = "sortable_" . Yii::app()->controller->route;
        $id = $this->grid->getId();
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery.ui');
        $cs->registerScript("sortable-grid-{$id}", $this->grid->initSortableScript(), CClientScript::POS_READY);

    }


    public function renderDataCellContent($row, $data)
    {
        echo CHtml::tag('i', ['class'=>'fa fa-arrows']);
    }


    public function renderDataCell($row)
    {
        $data = $this->grid->dataProvider->data[$row];
        $options = $this->htmlOptions;
        $options['class'] = 'sortable-column';
        $options['data-id'] = $data->primaryKey;
        echo CHtml::openTag('td', $options);
        $this->renderDataCellContent($row, $data);
        echo CHtml::closeTag('td');
    }


}
