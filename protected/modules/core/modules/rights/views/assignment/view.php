<? $this->widget('SGridView', array(
    'dataProvider'=>$dataProvider,
    'showButtonsColumn'=>false,
    'columns'=>array(
		array(
			'name'=>'name',
			'header'=>Rights::t('core', 'Name'),
			'type'=>'raw',
			'htmlOptions'=>array('class'=>'name-column'),
			'value'=>'$data->getAssignmentNameLink()',
		),
		array(
			'name'=>'assignments',
			'header'=>Rights::t('core', 'Roles'),
			'type'=>'raw',
			'htmlOptions'=>array('class'=>'role-column'),
			'value'=>'$data->getAssignmentsText(CAuthItem::TYPE_ROLE)',
		),
		array(
			'name'=>'assignments',
			'header'=>Rights::t('core', 'Tasks'),
			'type'=>'raw',
			'htmlOptions'=>array('class'=>'task-column'),
			'value'=>'$data->getAssignmentsText(CAuthItem::TYPE_TASK)',
		),
		array(
			'name'=>'assignments',
			'header'=>Rights::t('core', 'Operations'),
			'type'=>'raw',
			'htmlOptions'=>array('class'=>'operation-column'),
			'value'=>'$data->getAssignmentsText(CAuthItem::TYPE_OPERATION)',
		),
    )
)); ?>

    