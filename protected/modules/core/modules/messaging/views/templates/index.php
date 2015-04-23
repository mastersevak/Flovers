<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters']]); ?>
</div>
<? $this->widget('SGridView', array(
	'id' => 'notification-template-grid',
	'dataProvider' => $model->search(false, $type),
	'filter' => $model,
	// 'flexible'	=> true,
	'enableHistory'	=> false,
	'type'	=> 'striped bordered',
	'style'	=> 'blue',
	'columns' =>[
		[
			'name' => 'key',
			'type' => 'raw',
			'headerHtmlOptions' => ['width' => 100],
			'value' => function($data){return $data->key;}
		],
		[
			'name' => 'title',
			'type' => 'raw',
			'headerHtmlOptions' => ['width' => 100],
			'value' => function($data){return $data->title;}
		],
		[
			'name' => 'subject',
			'type' => 'raw',
			'headerHtmlOptions' => ['width' => 50],
			'value' => function($data){return $data->subject;}
		],
		[
			'class' => "SButtonColumn",
		]
	],
)); ?>
