<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['deleteSelected' => ['model' => get_class($model)], 'clearFilters'], 'size'=>'small']); ?>
</div>

<? $this->widget('SGridView', [
	'id' => 'email-gridview',
	'dataProvider' => $model->search(false, $level),
	'filter'	=> $model,
	// 'flexible'	=> true,
	'enableHistory'	=> false,
	'type'		=> 'striped bordered',
	'style'		=> 'blue',
	'columns'	=> [
		[
			'class'=>'SDateColumn',
			'name' => 'logtime',
			'type' => 'raw',
			'headerHtmlOptions' => ['width'=>200],
			'value' => function($data){
				return CHtml::tag('span', ['rel'=>"tooltip"], 
						date('d-m-Y H:i:s', $data->logtime));
			}
		],
		[
			'name'  => 'level',
			'type' 	=> 'html',
			'headerHtmlOptions' => ['width' => 180],
			'value' => function($data){return $data->level;},
			'filter' => false,
		],
		[
			'name'  => 'category',
			'type' 	=> 'html',
			'headerHtmlOptions' => ['width' => 180],
			'value' => function($data){return $data->category;},
		],
		[
			'name'  => 'message',
			'type' 	=> 'html',
			'value' => function($data){return $data->message;},
		],
		[   
			'class' => 'SButtonColumn',
			'headerHtmlOptions' => ['width' => 40],
			'template' => '{delete}',
			'deleteButtonUrl' => 'url("/core/messaging/logs/default/delete/", ["id"=>$data->id, "model" => get_class($data)])',
		],
	],
]);