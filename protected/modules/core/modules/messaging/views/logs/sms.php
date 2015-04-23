<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['deleteSelected' => ['model' => get_class($model)], 'clearFilters'], 'size'=>'small']); ?>
</div>

<? $this->widget('SGridView', [
	'id' => 'sms-gridview',
	'dataProvider' => $model->search(),
	'filter'	=> $model,
	// 'flexible'	=> true,
	'enableHistory'	=> false,
	'type'		=> 'striped bordered',
	'style'		=> 'blue',
	'columns'	=> [
		[
			'class'=>'SDateColumn',
			'name' => 'created',
			'type' => 'raw',
			'headerHtmlOptions' => ['width'=>200],
			'value' => function($data){
				return CHtml::tag('span', ['rel'=>"tooltip", 'title' => $data->id_creator ? User::listData()[$data->id_creator] : ''],
						date('d-m-Y H:i:s', strtotime($data->created)));
			}
		],
		[
			'name'  => 'phone',
			'type' 	=> 'html',
			'headerHtmlOptions' => ['width' => 150],
			'value' => function($data){return $data->phone;},
		],
		[
			'name'  => 'body',
			'type' 	=> 'html',
			'value' => function($data){return $data->body;},
		],
		[
			'name'  => 'queue_status',
			'type' 	=> 'raw',
			'headerHtmlOptions' => ['width' => 130],
			'filter'=> Lookup::items('SendStatus'),
			'value' => function($data){
				return $data->queueStatus();
			}
		],
		[   
			'class' => 'SButtonColumn',
			'headerHtmlOptions' => ['width' => 40],
			'template' => '{delete}',
			'deleteButtonUrl' => 'url("/core/messaging/logs/delete/", ["id"=>$data->id, "model" => get_class($data)])',
		],
	],
]);
?>