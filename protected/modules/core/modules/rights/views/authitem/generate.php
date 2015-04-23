<h3><?php echo Rights::t('core', 'Generate items'); ?></h3>
<p><?php echo Rights::t('core', 'Please select which items you wish to generate.'); ?></p>

<div class="form generated-item-list">
	<?php $form=$this->beginWidget('CActiveForm'); ?>
		<div class="row">
			<ul>
				<li class="main-node">
					<p class="node-title"><?php echo Rights::t('core', 'Application'); ?></p>
					<?php $this->renderPartial('_generateItems', array(
						'model'=>$model,
						'form'=>$form,
						'items'=>$items,
						'showModules' => false,
						'existingItems'=>$existingItems, // ?
					)); ?>
				</li>

				<li class="main-node">
					<p class="node-title"><?php echo Rights::t('core', 'Modules'); ?></p>
					<ul class="modules">
					<? foreach($items['modules'] as $moduleTitle => $module): ?>
						<li>
							<p class="module-title"><?=ucfirst($moduleTitle)?></p>

							<?php $this->renderPartial('_generateItems', array(
								'model'=>$model,
								'form'=>$form,
								'items'=>$module,
								'showModules' => true,
								'moduleName' => ucfirst($moduleTitle),
								'existingItems'=>$existingItems, // ?
							)); ?>
						</li>
					<? endforeach ?>
					</ul>
				</li>
			</ul>
			
		</div>

		<div class="row mt10">
				<?php echo CHtml::link(Rights::t('core', 'Select all'), '#', array(
					'onclick'=>"jQuery('.generate-item-table').find(':checkbox').attr('checked', 'checked'); return false;",
					'class'=>'selectAllLink')); ?>
				/
			<?php echo CHtml::link(Rights::t('core', 'Select none'), '#', array(
				'onclick'=>"jQuery('.generate-item-table').find(':checkbox').removeAttr('checked'); return false;",
				'class'=>'selectNoneLink')); ?>
		</div>

		<div class="row mt20 mb30">
			<?php echo CHtml::submitButton(Rights::t('core', 'Generate'), ['class'=>'btn btn-success']); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>