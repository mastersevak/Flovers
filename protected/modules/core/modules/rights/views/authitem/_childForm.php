
<?php $form=$this->beginWidget('CActiveForm'); ?>
	
	<?php echo $form->dropDownList($model, 'itemname', $itemnameSelectOptions); ?>
	<?php echo CHtml::submitButton(Rights::t('core', 'Add'), ['class'=>'btn']); ?>
	<?php echo $form->error($model, 'itemname'); ?>

<?php $this->endWidget(); ?>

