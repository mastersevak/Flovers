<div class="buttons mb20">
<?php
$this->widget('UIButtons', ['buttons'=>[
                'create' => [
                    'onclick'=>'UIButtons.gotoUrl(this)',
                    'data-url'=>$this->createUrl('authItem/create', array('type'=>CAuthItem::TYPE_TASK))
                ],
                'deleteselected'=>[
                    'onclick'=>'deleteSelectedOperations(this)',
                    'data-url'=>$this->createUrl('authItem/deleteSelected')
                ]
            ]]);
?>    
</div>

<div class="mb20">
    <?php echo Rights::t('core', 'A task is a permission to perform multiple operations, for example accessing a group of controller action.'); ?><br />
    <?php echo Rights::t('core', 'Tasks exist below roles in the authorization hierarchy and can therefore only inherit from other tasks and/or operations.'); ?>
</div>

<div id="tasks">

	<?php $this->widget('SGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
	    'emptyText'=>Rights::t('core', 'No tasks found.'),
	    'htmlOptions'=>array('class'=>'grid-view task-table'),
        'showButtonsColumn'=>false,
	    'columns'=>array(
    		array(
                'name'=>'name',
                'htmlOptions'=>array('class'=>'hidden authname'),
                'headerHtmlOptions'=>['class'=>'hidden']
            ),
            array(
    			'name'=>'name',
    			'header'=>Rights::t('core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    			'value'=>'$data->getGridNameLink()',
    		),
    		array(
    			'name'=>'description',
    			'header'=>Rights::t('core', 'Description'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'description-column'),
    		),
    		array(
    			'name'=>'bizRule',
    			'header'=>Rights::t('core', 'Business rule'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'bizrule-column'),
    			'visible'=>Rights::module()->enableBizRule===true,
    		),
    		array(
    			'name'=>'data',
    			'header'=>Rights::t('core', 'Data'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'data-column'),
    			'visible'=>Rights::module()->enableBizRuleData===true,
    		),
    		array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column'),
    			'value'=>'$data->getDeleteTaskLink()',
    		),
	    )
	)); ?>

	<div class="hint"><?php echo Rights::t('core', 'Values within square brackets tell how many children each item has.'); ?></div>

</div>