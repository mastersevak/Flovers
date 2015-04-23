<?php

Yii::import('zii.widgets.grid.CButtonColumn');

/**
 * Used to set buttons to use Glyphicons instead of the defaults images.
 */
class StatusButtonColumn extends CButtonColumn{

    public $value; 
    public $action;

    public $statusLabels = array();

    public $template = '{status}';
    
    public $statusButtonLabel;
    public $statusButtonUrl;
    public $statusButtonOptions=array('class'=>'status btn no_bg');
    
    public $afterStatus;

    public $filter;

    public $filterHtmlOptions = ['width'=>120];
    public $headerHtmlOptions = ['width'=>120];
    public $htmlOptions = ['width'=>120];

    public $name;

    public $deleteConfirmation;

    public $sortable = true;


    public $colors = array(
        '0'=>'#eb2f30', 
        '1'=>'#4ea800', 
        '2'=>'#ffbd38');

    public $defaultColor = '#bbb';

    /**
     * added new button {status}.
     */
    public function init()
    {
        $this->initExtraButtons();
        if($this->name===null)
            $this->sortable=false;
        if($this->name===null && $this->value===null)
            throw new CException(Yii::t('zii','Either "name" or "value" must be specified for CDataColumn.'));
        
        $this->htmlOptions = CMap::mergeArray(['class'=>'button-column'], $this->htmlOptions);

        parent::init();
    }

    /**
     * for new button {status}.
     */
    protected function initExtraButtons()
    {

        if(!$this->statusButtonUrl)
            $this->statusButtonUrl = str_replace("{status}", $this->action, 'Yii::app()->controller->createUrl("{status}",array("id"=>$data->primaryKey))');
        
        if(is_string($this->deleteConfirmation))
            $confirmation=$this->deleteConfirmation;
        else
            $confirmation=t('admin', 'Are you sure you want to change status?');

        foreach(array('status') as $id)
        {
            $button=array(
                'id'=>$id,
                'label'=>$this->{$id.'ButtonLabel'},
                'url'=>$this->statusButtonUrl,
                'options'=>$this->{$id.'ButtonOptions'},
            );
            if(isset($this->buttons[$id]))
                $this->buttons[$id]=array_merge($button, $this->buttons[$id]);
            else
                $this->buttons[$id]=$button;
        }

        if(!isset($this->buttons['status']['click']))
        {

            if(Yii::app()->request->enableCsrfValidation)
            {
                $csrfTokenName = Yii::app()->request->csrfTokenName;
                $csrfToken = Yii::app()->request->csrfToken;
                $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
            }
            else
                $csrf = '';

            if($this->afterStatus===null)
                $this->afterStatus='function(){}';

            $statuslabels = json_encode($this->filter);

            $this->buttons['status']['click']=<<<EOD
function(e) {
    e.preventDefault();

    var th=this;
    var afterStatus=$this->afterStatus;
    url = jQuery(this).attr('href');
        
    jQuery('#{$this->grid->id}').yiiGridView.update('{$this->grid->id}', {
        type:'POST',
        data: {fieldName: '{$this->name}'},
        url: url, $csrf
        success:function(data) {
            $.fn.yiiGridView.update('{$this->grid->id}');
            afterStatus(th,true,data);
        },
        error:function(XHR) {
            return afterStatus(th,false,XHR);
        }
    });
    
    
}
EOD;
        }
    }

    /**
     * flip status label
     */
    protected function renderButton($id, $button, $row, $data)
    {
        if (isset($button['id']) && $button['id'] == 'status') {
            $statusValue = $this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
            $button['label'] = $this->filter[$statusValue];
            $button['options']['style'] = "background-color:".
                (isset($this->colors[$statusValue]) ? $this->colors[$statusValue] : $this->defaultColor);
        }
        parent::renderButton($id,$button,$row,$data);
    }   

    public function renderHeaderCell()
    {
        $this->headerHtmlOptions['class'] = actual($this->headerHtmlOptions['class'], '');
        if($this->grid->showHiddenColumns) $this->headerHtmlOptions['class'] .= ' has-hidden-elements';

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

    /**
     * Renders the filter cell content.
     * This method will render the {@link filter} as is if it is a string.
     * If {@link filter} is an array, it is assumed to be a list of options, and a dropdown selector will be rendered.
     * Otherwise if {@link filter} is not false, a text field is rendered.
     * @since 1.1.1
     */
    protected function renderFilterCellContent()
    {
        if(is_string($this->filter))
            echo $this->filter;
        elseif($this->filter!==false && $this->grid->filter!==null && $this->name!==null && strpos($this->name,'.')===false)
        {
            if(is_array($this->filter))
                echo UIHelpers::dropDownList($this->grid->filter, $this->name, $this->filter, 
                    array('id'=>false,'prompt'=>'', 'data-width'=>'100%'));
            elseif($this->filter===null)
                echo CHtml::activeTextField($this->grid->filter, $this->name, array('id'=>false));
        }
        else
            parent::renderFilterCellContent();
    }


    /**
     * Renders the header cell content.
     * This method will render a link that can trigger the sorting if the column is sortable.
     */
    protected function renderHeaderCellContent()
    {
        if($this->grid->enableSorting && $this->sortable && $this->name!==null)
            echo $this->grid->dataProvider->getSort()->link($this->name,$this->header,array('class'=>'sort-link'));
        elseif($this->name!==null && $this->header===null)
        {
            if($this->grid->dataProvider instanceof CActiveDataProvider)
                echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
            else
                echo CHtml::encode($this->name);
        }
        else
            parent::renderHeaderCellContent();
    }


}
