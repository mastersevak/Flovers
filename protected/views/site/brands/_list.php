<?$this->widget('SListView', [
	'id' => "brands-list",
	'dataProvider' => $model->frontSearch(),
	'itemView' => 'brands/_listview',
	'ajaxUpdate' => true,
	'itemsTagName' => 'ul',
	'htmlOptions' => ['class' => 'wrapper'],
	'itemsCssClass' => 'items clearfix',
	'template' => '{items} {pager}'
]);?>