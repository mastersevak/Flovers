<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['deleteSelected' => ['model' => get_class($model)], 'clearFilters'], 'size'=>'small']); ?>
</div>

<? $this->widget('SGridView', [
	'id' => 'email-gridview',
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
			'name'  => 'email',
			'type' 	=> 'html',
			'headerHtmlOptions' => ['width' => 150],
			'value' => function($data){return $data->email;},
		],
		[
			'name'  => 'subject',
			'type' 	=> 'html',
			'headerHtmlOptions' => ['width' => 180],
			'value' => function($data){return $data->subject;},
		],
		[
			'name'  => 'message',
			'type' 	=> 'raw',
			'value' => function($data){
				$close = CHtml::link(CHtml::tag('i', ['class'=>'fa fa-reply mr5 c-gray'], ''), '#', 
                    ['class' => 'hidden hide-message', 'onclick' => "$.fn.logs('hideMessage', $(this)); return false;" ]); 	 
				$message = CHtml::tag('span', ['class' => 'hidden hidden-message'], $data->message);
				$show =	CHtml::link(CHtml::tag('i', ['class'=>'fa fa-eye mr5'], '')."ПОКАЗАТЬ", '#', 
                    ['class' => 'btn btn-mini block fbold show-message',
                    'onclick' => "$.fn.logs('showMessage', $(this)); return false;" ]); 
				
				return CHtml::tag('div', [], $close.$message.$show);
			},
		],
		[
			'name'  => 'status',
			'type' 	=> 'raw',
			'headerHtmlOptions' => ['width' => 130],
			'filter'=> Lookup::items('SendStatus'),
			'value' => function($data){
				return $data->queueStatus();
			}
		],
		[
			'name'  => 'error',
			'type' 	=> 'html',
			'headerHtmlOptions' => ['width' => 130],
			'htmlOptions' => ['class' => 'error'],
		],
		[   
			'class' => 'SButtonColumn',
			'headerHtmlOptions' => ['width' => 40],
			'template' => '{delete}',
			'deleteButtonUrl' => 'url("/core/messaging/logs/delete/", ["id"=>$data->id, "model" => get_class($data)])',
		],
	],
]);