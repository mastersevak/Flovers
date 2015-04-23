<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters', 'ShowImages']]); ?>
</div>

<? $this->widget('SGridView', array(
	'id' => 'grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'style' => 'blue',
	'columns'=>[
		 [
			'name' => 'thumbnail',
			'header' => '',
			'type' => 'raw',
			'filter' => false,
			'visible'=> $model->isShowThumbnail,
			'headerHtmlOptions' => ['width'=>40],
			'htmlOptions' => ['align'=>'center'],
			'value' => function($data){
				return CHtml::link($data->getThumbnail('thumb', 35, 35, $data->title), $data->backUrl, 
					['target' => '_blank', 'rel'=>'tooltip', 'title'=>'Открыть']);
			}
		],
		[
			'name'  => 'title',
			'type' => 'html',
			'value' => function($data){return CHtml::link($data->title, $data->backUrl);},
		],
		[
			'name'=>'slug',
			'value' => function($data){return CHtml::encode($data->slug);}
		],
		[
			'class' => 'StatusButtonColumn', 
			'name' => 'status',
			'action' => 'status', //<--action for this button
			'headerHtmlOptions' => ['width'=>100],
			'filter' => Lookup::items('StandartStatus'),
			'value' => '$data->status',
		],
		[
			'class'=>"SButtonColumn",
			'viewButtonUrl' => function($data){
				return '/'.lang().'/'.$data->url;
			}
		]
	]
)); ?>
