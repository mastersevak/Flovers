<div id="userAssignments" class="padding-all">
	
	<div class="row">
        <div class="col-md-6">

        <label>Пользователь: <strong><?=$model->username?></strong></label>

		<?php $this->widget('SGridView', array(
			'dataProvider' => $dataProvider,
			'template' => '{items}',
			// 'hideHeader' => true,
			'emptyText' => Rights::t('core', 'This user has not been assigned any items.'),
            'showNumColumn' => false,
            'showButtonsColumn'=>false,
            'showCheckBoxColumn'=>false,
			'columns'=>array(
    			array(
    				'name'=>'type',
    				'header'=>Rights::t('core', 'Type'),
    				'type'=>'raw',
    				'htmlOptions'=>['class'=>'type-column'],
    				'value'=>'$data->getTypeText()',
    			),
                array(
                    'name'=>'name',
                    'header'=>Rights::t('core', 'Name'),
                    'type'=>'raw',
                    'htmlOptions'=>['class'=>'name-column'],
                    'value'=>'$data->getNameText()',
                ),
    			array(
    				'header'=>'&nbsp;',
    				'type'=>'raw',
                    'headerHtmlOptions'=>['width'=>100],
    				'htmlOptions'=>['class'=>'actions-column'],
    				'value'=>'$data->getRevokeAssignmentLink()',
    			),
			)
		)); ?>

	    </div>

        <div class="col-md-6">
    		
            <label><?php echo Rights::t('core', 'Assign item'); ?></label>

    		<?php if( $formModel!==null ): ?>

    			<div class="form">

    				<?php $this->renderPartial('_form', array(
    					'model'=>$formModel,
    					'itemnameSelectOptions'=>$assignSelectOptions,
    				)); ?>

    			</div>

    		<?php else: ?>

    			<p class="info"><?php echo Rights::t('core', 'No assignments available to be assigned to this user.'); ?>

    		<?php endif; ?>

        </div>

	</div>

</div>