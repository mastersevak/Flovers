<?$this->widget('SListView', [
	'id' => "persons-list",
	'dataProvider' => $model->frontSearch(),
	'itemView' => 'persons/_listview',
	'ajaxUpdate' => true,
	'itemsTagName' => 'ul',
	'htmlOptions' => ['class' => 'wrapper'],
	'itemsCssClass' => 'items clearfix',
	'template' => '{items} {pager}'
]);?>