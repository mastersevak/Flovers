<?php
/* @var $this BackController */
/* @var $model Page */
/* @var $form CActiveForm */
?>

<div class="wide form advanced_search">

<?php $form=$this->beginWidget('SActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row clearfix">
		<?=$form->label($model,'status'); ?>
		<span class="field w300 mb10">
			<?=$form->dropDownList($model, 'status', 
				 Lookup::items('StandartStatus'), 
				 array(
				 'class'=>'uniform',
				 'empty'=>t('admin','All'),
				 )); ?>
		</span>
		
	</div>

	<div class="row clearfix">
		<?=$form->label($model,'slug'); ?>
		<span class="field">
				<?=$form->textField($model,'slug',array('size'=>60,'maxlength'=>255)); ?>
		</span>
		
	</div>

	<div class="row clearfix">
		<?=$form->label($model,'title'); ?>
		<span class="field">
			<?=$form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		</span>
	</div>

	<div class="row buttons">
		<?=CHtml::link('<span>'.t('admin','Search').'</span>', '#', array('class'=>'submit btn btn_orange btn_search radius50') )?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->