<?php

Yii::import('zii.widgets.grid.CButtonColumn');

/**
 * Used to set buttons to use Glyphicons instead of the defaults images.
 */
class SButtonColumn extends CButtonColumn
{
    /**
     * @var boolean whether the ID in the button options should be evaluated.
     */
    public $evaluateID = false;
	/**
	 * @var string the view button icon (defaults to 'eye-open').
	 */
	public $viewButtonIcon = 'eye';
	/**
	 * @var string the update button icon (defaults to 'pencil').
	 */
	public $updateButtonIcon = 'pencil';
	/**
	 * @var string the delete button icon (defaults to 'trash').
	 */
	public $deleteButtonIcon = 'trash-o';

    public $htmlOptions = array('class'=>'button-column');	

    public $showViewButton = false;
    public $viewButtonOptions = [];

	/**
     * Private member to store internally the button definition
     *
     * @var array
     */
    private $_clearButton;

    /**
     * a PHP expression for determining whether the button is visible
     *
     * @var string
     */
    public $clearVisible = false;
    /**
     * JS code to be invoked when the button is clicked, this is invoked before clearing the form fields;
     * Returning false from this code fragment prevents the AJAX to be executed. Only use 'return' block when you want to stop further steps execution.
     *
     * @var string
     */
    public $onClick_BeforeClear;
    /**
     * JS code to be invoked when the button is clicked, this is invoked after clearing the form fields, before AJAX;
     * Returning false from this code fragment prevents the AJAX to be executed. Only use 'return' block when you want to stop further steps execution.
     *
     * @var string
     */
    public $onClick_AfterClear;

    /**
     * Associative array of html elements to be passed for the button
     * default is: array('class'=>'clear','id'=>'cbcwr_clear','style'=>'text-align:center;display:block;');
     *
     * @var array
     */
    public $clearHtmlOptions = array(
        'class'=>'btn btn_blue btn_stop', 
        'alt'=>'Clear Filters',
        'style'=>'height:28px; width:33px; background-position-y:-120px; text-align:center; display:block; margin:auto'
        );

    /**
     * image URL of the button. If not set or false, a text link is used
     * Default is: $this->grid->baseScriptUrl.'/delete.png'
     *
     * @var string
     */
    public $imageUrl;

    /**
     *
     * @var string
     */
    public $url; //url for clearfilters

    /**
     * Label tag to be used on the button when no URL is given
     * Default is: Clear Filters
     *
     * @var unknown_type
     */
    public $label = '&nbsp;';

	public function init()
    {

        //initializ variables
        $_customJS=null;
        $_beforeAjax=null;
        $_click=null;
        $_visible= false;
        $_options=null;
        $_imageUrl=null;

        //show view button
        if(!$this->showViewButton){
            $this->viewButtonOptions = CMap::mergeArray(array('style' => 'display:none'), $this->viewButtonOptions);
            $width = 50;
        }
        else{
           $width = 60;
        }

        $this->headerHtmlOptions = CMap::mergeArray(array('width'=>$width), $this->headerHtmlOptions);

        // call parent to initialize other buttons
        parent::init();
    }

	/**
	 * Initializes the default buttons (view, update and delete).
	 */
	protected function initDefaultButtons()
	{
		parent::initDefaultButtons();

		if ($this->viewButtonIcon !== false && !isset($this->buttons['view']['icon']))
			$this->buttons['view']['icon'] = $this->viewButtonIcon;
		if ($this->updateButtonIcon !== false && !isset($this->buttons['update']['icon']))
			$this->buttons['update']['icon'] = $this->updateButtonIcon;
		if ($this->deleteButtonIcon !== false && !isset($this->buttons['delete']['icon']))
			$this->buttons['delete']['icon'] = $this->deleteButtonIcon;

		
		if(is_string($this->deleteConfirmation))
			$confirmation=$this->deleteConfirmation;
		else
			$confirmation='Are you sure you want to delete selected item(s)?';

		if(Yii::app()->request->enableCsrfValidation)
		{
			$csrfTokenName = Yii::app()->request->csrfTokenName;
			$csrfToken = Yii::app()->request->csrfToken;
			$csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
		}
		else
			$csrf = '';

		if($this->afterDelete===null)
			$this->afterDelete='function(){}';

		$this->buttons['delete']['click']=<<<EOD
function(e) {
	e.preventDefault();
	url = jQuery(this).attr('href');
	jConfirm('$confirmation', 
		Yii.t('admin','Delete Items'), function(r) {
		if(r){	

			var th = this,
				afterDelete = $this->afterDelete;

			jQuery('#{$this->grid->id}').yiiGridView('update', {
				type: 'POST',
				url: url, $csrf
				success: function(data) {
					
					jQuery('#{$this->grid->id}').yiiGridView('update');
					afterDelete(th, true, data);
				},
				error: function(XHR) {
					return afterDelete(th, false, XHR);
				}
			});
		}
	});
	
}
EOD;
        if($this->showViewButton){
            $this->buttons['view']['options'] = array('target'=>'_blank');
        }
	}

    /**
     * Renders a link button.
     * @param string $id the ID of the button
     * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data object associated with the row
     */
    protected function renderButton($id, $button, $row, $data)
    {
        if (isset($button['visible']) && !$this->evaluateExpression($button['visible'], array('row'=>$row, 'data'=>$data)))
            return;

        $label = isset($button['label']) ? $button['label'] : $id;
        $url = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data'=>$data, 'row'=>$row)) : '#';

        $options = isset($button['options']) ? $button['options'] : array();

        if (!isset($options['title']))
            $options['title'] = $label;

        if (!isset($options['rel']))
            $options['rel'] = 'tooltip';

        if (isset($button['icon']))
        {
            if (strpos($button['icon'], 'icon') === false)
                $button['icon'] = 'fa fa-'.implode(' fa fa-', explode(' ', $button['icon']));

            echo CHtml::link('<i class="'.$button['icon'].'"></i>', $url, $options);
        }
        else if (isset($button['imageUrl']) && is_string($button['imageUrl']))
            echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
        else
            echo CHtml::link($label, $url, $options);
    }


    public function renderHeaderCell()
    {

        if(Cookie::get("hidden-column-{$this->id}")) { //для того чтобы скрытые колонки не рисовались
            $this->headerHtmlOptions['class'] = actual($this->headerHtmlOptions['class'], '') . ' hidden';
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
     * Renders the data cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    public function renderDataCellContent($row,$data)
    {
        $tr=array();
        ob_start();
        foreach($this->buttons as $id=>$button)
        {
            if($this->evaluateID and isset($button['options']['data-id'])) 
                $button['options']['data-id'] = $this->evaluateExpression($button['options']['data-id'], array('row'=>$row,'data'=>$data));

            $this->renderButton($id,$button,$row,$data);
            $tr['{'.$id.'}']=ob_get_contents();
            ob_clean();
        }
        ob_end_clean();
        echo strtr($this->template,$tr);
    }

	/**
         * Static method to check if a model uses a certain behavior class
         *
         * @param CModel $model
         * @param string $behaviorClass
         * @return boolean
         */
    private static function modelUsesBehavior($model,$behaviorClass) {
        $behaviors=$model->behaviors();
        if (is_array($behaviors)) {
            foreach ($behaviors as $behavior => $behaviorDefine) {
                if (is_array($behaviorDefine)) {
                    $className=$behaviorDefine['class'];
                } else {
                    $className=$behaviorDefine;
                }
                if (strpos($className,$behaviorClass)!==false) {
                    return true;
                }
            }
        }
        return false;
    }

	public static function clearFilters($controller, $model) {
        $model->unsetAttributes();
        Yii::import('core.behaviors.ERememberFiltersBehavior');
        
        try {
            if (self::modelUsesBehavior($model, 'ERememberFiltersBehavior')) {
    
                $model->unsetFilters();
    
            }
        }
        catch (Exception $e) {
    
        }
        //$controller->redirect(array($controller->action->ID));
    }

}
