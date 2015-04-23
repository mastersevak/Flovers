<?php 
/**
 * obьект CGridview который притерпел изменения в стилях
 */

Yii::import('zii.widgets.grid.CGridView');

class SGridView extends CGridView {

 	// Table types.
 	const TYPE_NORMAL = 'table';
	const TYPE_STRIPED = 'striped';
	const TYPE_BORDERED = 'bordered';
	const TYPE_HOVERED = 'hover';

	public $ajaxUpdate = true;
    public $style = '';
    public $enableHistory = true;

	/**
	 * @var string the URL of the CSS file used by this grid view.
	 * Defaults to false, meaning that no CSS will be included.
	 */
 	public $cssFile = false; //переопределяем данную переменную чтобы он не грузил свои стили

 	public $pager = ['class'=>'SPager'];
    public $pagerCssClass = "pagination";
    public $headerCssClass = '';

 	public $sortable = false;
 	public $sortAction = false;

 	public $showButtonsColumn = true;
 	public $showCheckBoxColumn = true;
    public $invisibleCheckBoxColumn = false;
    public $showNumColumn = true;
    public $showPageSize = true;
    public $showHiddenColumns = true;
    public $enableSelectAll = true;

    /* Нужно для того, чтобы при save and close, снова оказыватся на той странице где были */
 	public $gridIndex = true; 
    public $flexible = false; //для width 100%

    /**
     * Для тех таблиц к которым в конец можно динамически добавлять строки
     */
    public $autoIncrement = false;

    public $assetsUrl;

    public $afterAjaxUpdateFunctions = [];
    public $beforeAjaxUpdateFunctions = [];


 	/**
	 * @var array the table type.
	 * Valid values are 
	 * [TYPE_STRIPED, TYPE_BORDERED, TYPE_CONDENSED, TYPE_HOVERED]
	 */
 	public $type = 'hover bordered';
 	
 	public function init(){
 		parent::init();

        $this->baseScriptUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('core.widgets.common.assets.gridview'));

 		/*установка gridIndex
 		это делается для того, чтобы при save and close, снова оказыватся на той странице где были*/
 		if($this->gridIndex)
 			user()->setState('gridIndex', request()->requestUri);

        if(!$this->flexible)
            $this->htmlOptions['class'] .= ' iblock';

 		//установка стилей для таблицы
 		$this->itemsCssClass .= ' table' . ($this->flexible ? ' flexible' : '');
        if($this->style) $this->itemsCssClass .= " {$this->style}";

 		if($this->type){
 			if(is_numeric($this->type))
 				$this->type = [$this->type];
 			elseif(is_string($this->type))
 				$this->type = explode(' ', $this->type);
 			
 			array_walk($this->type, function(&$class, $key){
				$class = 'table-'.$class;
			});

			$classes = $this->type;
			$this->itemsCssClass .= ' ' . implode(' ',  $this->type);
 		}

        //before ajax update
        $beforeAjaxUpdateFunctions[] = "Gridview.beforeAjaxUpdate(id, options);";

        if($this->beforeAjaxUpdate)
            $beforeAjaxUpdateFunctions[] = "({$this->beforeAjaxUpdate})(id, options);";

        $this->beforeAjaxUpdate = "function(id, options){".
        implode(";\n", $beforeAjaxUpdateFunctions)."}";

        //after ajax update
        $this->afterAjaxUpdateFunctions[] = "Gridview.afterAjaxUpdate(id, data);"; 
        $sortableScript = $this->initSortableScript();
        $this->afterAjaxUpdateFunctions[] = "{$sortableScript}";

        if(!$this->flexible){
            $this->afterAjaxUpdateFunctions[] = "Gridview.setPageHeaderWidth('{$this->id}');";
            cs()->registerScript('update-page-header-width', "Gridview.setPageHeaderWidth('{$this->id}');", CClientScript::POS_READY);
        }

        if($this->afterAjaxUpdate) //если для таблицы объявлен afterAjaxUpdate
            $this->afterAjaxUpdateFunctions[] = "({$this->afterAjaxUpdate})(id, data);";

        $this->afterAjaxUpdate = "function(id, data){".
        implode(";\n", $this->afterAjaxUpdateFunctions)."}";

        if ($this->assetsUrl === null) {
            $recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
            $this->assetsUrl = assets(dirname(__FILE__).DS.'assets', false, -1, $recreate);
        }

 	}

    public static function assetsUrl(){
        $recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
        return assets(dirname(__FILE__).DS.'assets'.DS.'gridview', false, -1, $recreate);
    }

    //переопределяем
    public function registerClientScript(){
        parent::registerClientScript();
        //загружаем свои стили 
        cs()->registerCssFile( $this->baseScriptUrl . '/gridview.css');
        cs()->registerScriptFile( $this->baseScriptUrl . '/gridview.js');
    }

 	public function initColumns()
    {

    	//num column
        if($this->showNumColumn)
        $this->columns = CMap::mergeArray(
        	array(
        		array(
        			'header' => 'N',
					'headerHtmlOptions' => array('width'=>30),
					'htmlOptions' => array('align'=>'right'),
					'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',)
        	), $this->columns);

        //checkbox column
    	if ($this->showCheckBoxColumn === true)
        {
            $exists = false;
            foreach ($this->columns as $column)
                if (isset($column['class']) && $column['class'] == 'SCheckBoxColumn')
                    $exists = true;

            if ($exists === false)
                $this->columns = CMap::mergeArray([[
                        'class' => 'SCheckBoxColumn',
                        'headerHtmlOptions' => ['class' => 'checkbox-column' . ($this->invisibleCheckBoxColumn ? ' hidden' : ''), 'width' => 30],
                        'filterHtmlOptions' => ['class' => ($this->invisibleCheckBoxColumn ? ' hidden' : '')],
                        'htmlOptions' => ['class' => 'checkbox-column' . ($this->invisibleCheckBoxColumn ? ' hidden' : '')],
                        'footerHtmlOptions' => ['class' => ($this->invisibleCheckBoxColumn ? ' hidden' : '')]
                    ]], $this->columns);
        }

        //sortable 
        if ($this->sortable === true)
        {
            $exists = false;
            foreach ($this->columns as $column)
                if (isset($column['class']) && $column['class'] == 'core.behaviors.sortable.SortableColumn')
                    $exists = true;

            if ($exists === false)
                $this->columns = CMap::mergeArray(array(array('class' => 'core.behaviors.sortable.SortableColumn')), $this->columns);
        }

        //show buttons
        if ($this->showButtonsColumn === true)
        {
            $exists = false;
            foreach ($this->columns as $column)
                if (isset($column['class']) && $column['class'] == 'SButtonColumn')
                    $exists = true;

            if ($exists === false)
                array_push($this->columns, array('class' => 'SButtonColumn'));
        }


        // parent::initColumns();
        if($this->columns===array())
        {
            if($this->dataProvider instanceof CActiveDataProvider)
                $this->columns=$this->dataProvider->model->attributeNames();
            elseif($this->dataProvider instanceof IDataProvider)
            {
                // use the keys of the first row of data as the default columns
                $data=$this->dataProvider->getData();
                if(isset($data[0]) && is_array($data[0]))
                    $this->columns=array_keys($data[0]);
            }
        }
        $id=$this->getId();
        foreach($this->columns as $i=>$column)
        {
            if(is_string($column))
                $column=$this->createDataColumn($column);
            else
            {
                if(!isset($column['class']))
                    $column['class']='SDataColumn';
                $column=Yii::createComponent($column, $this);
            }
            if(!$column->visible)
            {
                unset($this->columns[$i]);
                continue;
            }
            if($column->id===null)
                $column->id=$id.'_c'.$i;
            $this->columns[$i]=$column;
        }

        foreach($this->columns as $column)
            $column->init();
    }

    //сумарная информация
    public function renderSummary(){
        //регистрация скрипта для работы js куки
        Yii::app()->clientScript->registerCoreScript('cookie');

        echo CHtml::openTag('div', ['class'=>'mb10']);

    	$model = $this->filter ? get_class($this->filter) : (isset($this->dataProvider->model) && $this->dataProvider->model ? get_class($this->dataProvider->model) : $this->id);
	
        if($this->showPageSize) $this->pageSizeWidget($model, $model != $this->id);
        if($this->showHiddenColumns) $this->hiddenColumnsWidget($model);
    
        //сумарное количество эелементов
    	$this->summaryCssClass = "summary";
        $this->summaryText = "{start} - {end} из {count}";
        parent::renderSummary();
        echo CHtml::tag('br', ['clear'=>'both']);
        echo CHtml::closeTag('div');
    }

    /**
     * Renders the pager.
     */
    public function renderPager(){
        echo CHtml::openTag('div', ['class'=>'row']);
            echo CHtml::openTag('div', ['class'=>'col-md-12']);
                parent::renderPager();
            echo CHtml::closeTag('div');
        echo CHtml::closeTag('div');
    }
	
	/**
	 * Функционал для Sortable Column
	 */
    public function initSortableScript(){
    	$id = $this->getId();

    	if(!$this->sortAction)
        	$url = '/' . preg_replace('#' . Yii::app()->controller->action->id . '$#', 'sorting', Yii::app()->controller->route);
        else 
        	$url = $this->sortAction;
        
        $script = "
            $('#{$id} tbody').sortable({
                handle: '.sortable-column',
                update : function (event, ui) {
                    var ids = [];
                    $('#{$id} .sortable-column').each(function(i) {
                        ids[i] = $(this).data('id');
                    });

                    jPost('{$url}', {ids : ids}, function(){});
                }
            });";

		return $script;

    }

    /**
     * CUSTOM FUNCTIONS
     */
    private function pageSizeWidget($model, $isModel = true){
        echo CHtml::openTag('div', array('class'=>'fl mr10')).
             UIHelpers::dropDownList('pageSize_'.$this->id, ($isModel ? Common::getPagerSize($model) : 15),
                ['1'=>'1', '2'=>'2','5'=>'5','10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '50'=>'50', '100'=>'100', '250'=>'250'],
                [
                    'class'=>'small',
                    'data-width'=>'60px',
                    'data-update'=> $model.'_pagesize',
                    'data-grid-id'=>$this->id,
                    'data-ajax-update'=>$this->ajaxUpdate,
                    'onchange'=>'Gridview.onPageSizeChange(this)']).
            
        CHtml::closeTag('div');
    }
     
    private function hiddenColumnsWidget(){
        //найти скрытые колонки
        $hiddenColumns = [];
        $index = 1;
        foreach($this->columns as $key => $column){
            if(!$column->visible) continue;
            
            if(Cookie::get("hidden-column-{$column->id}")){
                // $index ++;
                $header = '';
                if($column->header) $header = $column->header;
                else {
                    $labels = $this->dataProvider->model->attributeLabels();
                    if($column->name && isset($labels[$column->name])) $header = $labels[$column->name];
                    elseif($column->name) $header = $column->name;
                }
                $hiddenColumns["opencol_{$index}"] = $header;
            }
                
            $index  ++;
        }
        echo CHtml::openTag('div', array('class'=>'fl mr10')).
             UIHelpers::dropDownList('hiddenColumns_'.$this->id, '', $hiddenColumns,
                ['class'=>'show-table-columns small', 'data-width'=>'135px',  'empty' => 'Показать колонки']).
            CHtml::closeTag('div');
    }

    /**
     * Renders the table header.
     */
    public function renderTableHeader()
    {
        if(!$this->hideHeader)
        {
            echo "<thead>\n";

            if($this->filterPosition===self::FILTER_POS_HEADER)
                $this->renderFilter();

            echo "<tr ".($this->headerCssClass ? "class='".$this->headerCssClass."'" : '').">\n";
            foreach($this->columns as $column)
                $column->renderHeaderCell();
            echo "</tr>\n";

            if($this->filterPosition===self::FILTER_POS_BODY)
                $this->renderFilter();

            echo "</thead>\n";
        }
        elseif($this->filter!==null && ($this->filterPosition===self::FILTER_POS_HEADER || $this->filterPosition===self::FILTER_POS_BODY))
        {
            echo "<thead>\n";
            $this->renderFilter();
            echo "</thead>\n";
        }
    }

	
 }