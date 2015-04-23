<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters']]); ?>
</div>

<? $this->widget('SGridView', array(
	'id' => 'grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'style' => 'blue',
	'columns'=>array(
		array(
			'name'  => 'title',
		),
		array(
			'name'=>'slug',
			'value' => function($data){return CHtml::encode($data->slug);}
		),
		array(
			'class' => 'StatusButtonColumn', 
			'name' => 'status',
			'action' => 'status', //<--action for this button
            'headerHtmlOptions' => array('width'=>100),
            'filter' => Lookup::items('StandartStatus'),
            'value' => '$data->status',
		),
		array(
			'class'=>"SButtonColumn",
			'showViewButton' => true,	
			'viewButtonUrl' => function($data){
				return '/'.lang().'/'.$data->url;
			}
		)
	),
)); ?>