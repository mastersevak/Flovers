<?

/**
* SListView
*/

Yii::import('zii.widgets.CListView');

class SListView extends CListView
{
	
	public $sorterList;

    public $cssFile = false;

    public $ajaxUpdate = false;
    public $showPageSize = true;

    public function init(){

        //register scripts
        $assetsUrl = assets(dirname(__FILE__).'/assets');

        $this->cssFile = $assetsUrl."/listview/style.css";

        parent::init();

        //before ajax update
        $beforeAjaxUpdateFunctions[] = "Listview.beforeAjaxUpdate(id, options);";

        if($this->beforeAjaxUpdate)
            $beforeAjaxUpdateFunctions[] = "({$this->beforeAjaxUpdate})(id, options);";

        $this->beforeAjaxUpdate = "function(id, options){".
        implode(";\n", $beforeAjaxUpdateFunctions)."}";

        //after ajax update
        $afterAjaxUpdateFunctions[] = "Listview.afterAjaxUpdate(id, data);";

        if($this->afterAjaxUpdate) //если для таблицы объявлен afterAjaxUpdate
            $afterAjaxUpdateFunctions[] = "({$this->afterAjaxUpdate})(id, data);";

        $this->afterAjaxUpdate = "function(id, data){".
        implode(";\n", $afterAjaxUpdateFunctions)."}";

        cs()->registerCssFile( $assetsUrl. '/listview/sorter.css');
        cs()->registerScriptFile( $assetsUrl. '/listview/listview.js');
    }

    //сумарная информация
    public function renderSummary(){
        //регистрация скрипта для работы js куки
        Yii::app()->clientScript->registerCoreScript('cookie');

        echo CHtml::openTag('div', ['class'=>'mb10 fl']);

    	$model = isset($this->dataProvider->model) && $this->dataProvider->model ? get_class($this->dataProvider->model) : $this->id;
	
        if($this->showPageSize) $this->pageSizeWidget($model, $model != $this->id);
    
        //сумарное количество эелементов
        $this->summaryText = "{start} - {end} из {count}";
        parent::renderSummary();
        echo CHtml::tag('br', ['clear'=>'both']);
        echo CHtml::closeTag('div');
    }

    /**
     * CUSTOM FUNCTIONS
     */
    private function pageSizeWidget($model, $isModel = true){
        echo CHtml::openTag('div', array('class'=>'fl mr10')).
        	 CHtml::tag('span', ['class' => 'fsize12 c-gray fl mr5 mt5'], 'Показать').
             UIHelpers::dropDownList('pageSize_'.$this->id, ($isModel ? Common::getPagerSize($model) : 15),
                ['1'=>'1', '2'=>'2','5'=>'5','10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '50'=>'50', '100'=>'100', '250'=>'250'],
                [
                    'class'=>'small fl pagesize',
                    'data-width'=>'60px',
                    'data-update'=> $model.'_pagesize',
                    'data-list-id'=>$this->id,
                    'data-ajax-update'=>$this->ajaxUpdate,
                    'onchange'=>'Listview.onPageSizeChange(this)']).
            
        CHtml::closeTag('div');
    }
}