<?$this->widget('EListView', [
	'dataProvider' => $dataProvider,
	'itemView' => 'products/'.$itemView,
	'ajaxUpdate' => false,
	'itemsCssClass' => 'items clearfix',
	'template' => '{items}',
]);?>