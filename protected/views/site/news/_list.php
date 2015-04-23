<?$this->widget('SListView', [
	'id'				=>	"news-list",
	'dataProvider'		=>	$model->frontSearch(),
	'itemView'			=>	'news/_listview',
	'ajaxUpdate'		=>	true,
	'itemsTagName'		=>	'ul',
	'htmlOptions'		=>	['class' => 'wrapper'],
	'itemsCssClass'		=>	'items clearfix',
	'template'			=>	'{items} {pager}',
	'enablePagination'	=>	true,
	'pagerCssClass'		=>	'pagination',
]);?>