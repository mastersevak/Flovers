<div class="grid simple mt20">
	<div class="buttons mb20">
		<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters'], 'size'=>'small']); ?>
	</div>

	<?$this->widget('SGridView', array(
		'id' => 'grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'sortable'=>true,
		'flexible'=>true,
		'type' => 'striped bordered',
		'style' => 'blue',
		'columns'=>array(
			[
				'name'  => 'type',
				'type' => 'html',
				'value' => function($data){return CHtml::link($data->type, $data->backUrl);},
			],
			[
				'name'  => 'name',
			],
			[
				'name'=>'code',
				'headerHtmlOptions' => ['width'=>40]
			]
		),
	));?>
</div>