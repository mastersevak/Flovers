<div class="buttons mb20">
	<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters']]); ?>
</div>

<? $this->widget('SGridView', array(
	'id' => 'grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'style' => 'blue',
	'columns'=>[
		[
			'name'  => 'title',
		],
		[
			'name'=>'slug',
			'value' => function($data){return CHtml::encode($data->slug);}
		],
	],
)); ?>