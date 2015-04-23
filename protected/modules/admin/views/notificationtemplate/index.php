<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>[
			'create',
			'DeleteSelected' => ['model'=>'NotificationTemplate'], 'clearFilters'
		], 'size' => 'small']) ?>
</div>
<? $this->widget('SGridView', array(
	'id' => 'notification-template-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'showButtonsColumn' => true,
	'showNumColumn' => false,
	'columns' =>[
		[
			'name' => 'id',
			'header' => 'ID',
			'type' => 'raw',
			'headerHtmlOptions' => ['width'=>20],
			'value' => function($data){return $data->id;}
		],
		[
			'name' => 'slug',
			'type' => 'raw',
			'headerHtmlOptions' => ['width'=>100],
			'value' => function($data){return $data->slug;}
		],
		[
			'name' => 'title',
			'type' => 'raw',
			'headerHtmlOptions' => ['width'=>100],
			'value' => function($data){return $data->title;}
		],
		[
			'name' => 'subject',
			'type' => 'raw',
			'headerHtmlOptions' => ['width'=>200],
			'value' => function($data){return $data->subject;}
		],
		[
			'name' => 'type',
            'headerHtmlOptions' => ['width'=>100],
            'filter' => NotificationTemplate::$types,
            'value' => function($data){return NotificationTemplate::$types[$data->type];}
		],
	],
)); ?>
