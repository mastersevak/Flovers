<div class="grid simple mt20">
	<div class="buttons mb20">
		<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters'], 'size'=>'small']); ?>
	</div>

	<? $this->widget('SGridView', array(
		'id' => 'grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'sortable'=>true,
		'flexible'=>true,
		'columns'=>array(
			array(
				'name'  => 'type',
				'type' => 'html',
				'value' => function($data){return CHtml::link($data->type, $data->backUrl);},
			),
			array(
				'name'  => 'name',
				'type' => 'html',
				'value' => function($data){return CHtml::link($data->l_name, $data->backUrl);},
			),
			array(
				'name'=>'code',
				'headerHtmlOptions' => array('width'=>40)
			)
		),
	));  ?>
</div>

