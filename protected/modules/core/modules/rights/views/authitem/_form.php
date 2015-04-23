

<?php if( $model->scenario==='update' ): ?>

	<h3><?php echo Rights::getAuthItemTypeName($model->type); ?></h3>

<?php endif; ?>
	
<?php $form=$this->beginWidget('CActiveForm'); ?>
<div class="std-form">
	<div class="control-group">
		<?php echo $form->labelEx($model, 'name'); ?>
		<span class="field">
			<?php echo $form->textField($model, 'name', array('maxlength'=>255, 'class'=>'text-field')); ?>
			<?php echo $form->error($model, 'name'); ?>
			<p class="hint"><?php echo Rights::t('core', 'Do not change the name unless you know what you are doing.'); ?></p>
		</span>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'description'); ?>
		<span class="field">
			<?php echo $form->textField($model, 'description', array('maxlength'=>255, 'class'=>'text-field')); ?>
			<?php echo $form->error($model, 'description'); ?>
			<p class="hint"><?php echo Rights::t('core', 'A descriptive name for this item.'); ?></p>
		</span>
	</div>

	<?php if( Rights::module()->enableBizRule===true ): ?>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'bizRule'); ?>
			<span class="field">
				<?php echo $form->textField($model, 'bizRule', array('maxlength'=>255, 'class'=>'text-field')); ?>
				<?php echo $form->error($model, 'bizRule'); ?>
				<p class="hint"><?php echo Rights::t('core', 'Code that will be executed when performing access checking.'); ?></p>
			</span>
		</div>

	<?php endif; ?>

	<?php if( Rights::module()->enableBizRule===true && Rights::module()->enableBizRuleData ): ?>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'data'); ?>
			<span class="field">
				<?php echo $form->textField($model, 'data', array('maxlength'=>255, 'class'=>'text-field')); ?>
				<?php echo $form->error($model, 'data'); ?>
				<p class="hint"><?php echo Rights::t('core', 'Additional data available when executing the business rule.'); ?></p>
			</span>
		</div>

	<?php endif; ?>

</div>

<div class="mt20 buttons">
	<?$this->widget('UIButtons', ['buttons'=>['save', 'close'=>['data-url'=>Yii::app()->user->rightsReturnUrl]]])?>
</div>
<?php $this->endWidget(); ?>