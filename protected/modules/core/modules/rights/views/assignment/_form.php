<div class="form">

<?php $form=$this->beginWidget('CActiveForm'); ?>
	
	<div class="fl mb10">
		<?php echo $form->dropDownList($model, 'itemname', $itemnameSelectOptions); ?>
		<?php echo $form->error($model, 'itemname'); ?>
	</div>
	
	<div class="buttons">
		<?php echo CHtml::submitButton(Rights::t('core', 'Assign'), ['class'=>'btn ml30']); ?>
	</div>

<?php $this->endWidget(); ?>

</div>