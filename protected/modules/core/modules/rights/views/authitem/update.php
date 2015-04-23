<?php $this->renderPartial('_form', array('model'=>$formModel)); ?>

<div>

	<h3><?php echo Rights::t('core', 'Relations'); ?></h3>

	<?php if( $model->name!==Rights::module()->superuserName ): ?>

	<div class="std-form">
		<div class="control-group">
			<label><?php echo Rights::t('core', 'Parents'); ?></label>
			<span class="field clearfix">
				<?php $this->widget('SGridView', array(
					'dataProvider'=>$parentDataProvider,
					'template'=>'{items}',
					'hideHeader'=>true,
					'emptyText'=>Rights::t('core', 'This item has no parents.'),
					'htmlOptions'=>array('class'=>'parents-table'),
					'showButtonsColumn' => false,
					'showNumColumn' => false,
					'showCheckBoxColumn' => false,
					'type'=>'striped bordered',
					'columns'=>array(
    					array(
    						'name'=>'name',
    						'header'=>Rights::t('core', 'Name'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'name-column'),
    						'value'=>'$data->getNameLink()',
    					),
    					array(
    						'name'=>'type',
    						'header'=>Rights::t('core', 'Type'),
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'type-column', 'width'=>200),
    						'value'=>'$data->getTypeText()',
    					),
    					array(
    						'header'=>'&nbsp;',
    						'type'=>'raw',
    						'htmlOptions'=>array('class'=>'actions-column', 'width'=>100),
    						'value'=>'',
    					),
					)
				)); ?>
			</span>
		</div>			

		<div class="control-group">
			<label><?php echo Rights::t('core', 'Children'); ?></label>
			<span class="field clearfix">
				<?php $this->widget('SGridView', array(
					'dataProvider'=>$childDataProvider,
					'template'=>'{items}',
					'hideHeader'=>true,
					'emptyText'=>Rights::t('core', 'This item has no children.'),
					'htmlOptions'=>array('class'=>'children-table'),
					'showButtonsColumn' => false,
					'showNumColumn' => false,
					'showCheckBoxColumn' => false,
					'type'=>'striped bordered',
					'columns'=>array(
						array(
							'name'=>'name',
							'header'=>Rights::t('core', 'Name'),
							'type'=>'raw',
							'htmlOptions'=>array('class'=>'name-column'),
							'value'=>'$data->getNameLink()',
						),
						array(
							'name'=>'type',
							'header'=>Rights::t('core', 'Type'),
							'type'=>'raw',
							'htmlOptions'=>array('class'=>'type-column', 'width'=>200),
							'value'=>'$data->getTypeText()',
						),
						array(
							'header'=>'&nbsp;',
							'type'=>'raw',
							'htmlOptions'=>array('class'=>'actions-column', 'width'=>100),
							'value'=>'$data->getRemoveChildLink()',
						),
					)
				)); ?>
			</span>
		</div>


		<div class="control-group">
			<label><?php echo Rights::t('core', 'Add Child'); ?></label>
			<span class="field clearfix">
			<?php if( $childFormModel!==null ): ?>

				<?php $this->renderPartial('_childForm', array(
					'model'=>$childFormModel,
					'itemnameSelectOptions'=>CMap::mergeArray($childSelectOptions, array(
							'style' => ''
						)),
				)); ?>

			<?php else: ?>

				<p class="info">
					<?php echo Rights::t('core', 'No children available to be added to this item.'); ?>
				</p>

			<?php endif; ?>
			</span>
		</div>
	</div>

	<?php else: ?>
	<p>
		<?php echo Rights::t('core', 'No relations need to be set for the superuser role.'); ?><br />
		<?php echo Rights::t('core', 'Super users are always granted access implicitly.'); ?>
	</p>
	<?php endif; ?>

</div>