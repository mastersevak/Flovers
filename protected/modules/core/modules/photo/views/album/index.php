<?php
$this->breadcrumbs = array(t('photoalbum', 'Фотоальбомы'));

?>


<div class="filemgr_head">
	<? $this->renderPartial('//main/index-buttons', 
		array('model'=>get_class($model), 'showimages'=>true)); ?>

	<div class="search-form" style="display:none">
		<?php $this->renderPartial('/album/_search',array(
			'model'=>$model,
		)); ?>
	</div><!-- search-form -->
</div>

<? $this->widget('SGridView', array(
	'id' => 'grid',
	'dataProvider'=>$provider,
	'filter'=>$model,
	'sortable'=>true,
	'columns'=>array(
		array(
			'name' => 'thumbnail',
			'type' => 'raw',
			'filter' => false,
			'visible'=> $model->isShowThumbnail,
			'headerHtmlOptions' => array('width'=>80),
			'value' => function($data){
				return CHtml::link($data->getThumbnail('thumb', 80), $data->backUrl);
			}
		),
		array(
			'class'=>'SDateColumn',
			'name'=>'date',
			'filter'=>Common::datePicker($model, 'date', true),
		),
		array(
			'name'  => 'title',
			'type' => 'html',
			'value' => function($data){return CHtml::link($data->l_title, $data->backUrl);},
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
			'showViewButton' =>true,
			'viewButtonUrl' => '$data->url'
		),
	),
)); 

?>







