<?php if ($model): ?>

<?php $form=$this->beginWidget('SActiveForm', array(
	'id' => 'ajax-page-meta',
	'action' => '/core/page/back/ajaxupdate', 'id'=>$model->id),
	'enableAjaxValidation'=>true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
		'afterValidate'=>'js:function(form, data, hasError){
			if(!hasError){
				$.post(form.prop("action"), form.serialize());
			}
		}'
	)
)); ?>
	<h4 class="mb10"><?=t('front', 'Мета информация');?></h4 class="mb20">
   	<div class="stdform stdform2">
   		<div class="par">
			<?=$form->labelEx($model,'meta_title'); ?>
			<span class="field">
				<?=$form->textField($model,'meta_title',
						array('maxlength'=>255, 'data-limit'=>65, 'data-language'=>lang())); ?>
				<?=$form->error($model,'meta_title'); ?>
			</span>
		</div>

		<div class="par">
			<?=$form->labelEx($model,'meta_description'); ?>
			<span class="field">
				<?=$form->textArea($model,'meta_description',
						array('maxlength'=>255, 'data-limit'=>170, 'data-language'=>lang())); ?>
				<?=$form->error($model,'meta_description'); ?>
			</span> 
		</div>	

		<div class="par">
			<?=$form->labelEx($model,'meta_keywords'); ?>
			<span class="field">
				<?=$form->textArea($model,'meta_keywords',
						array('maxlength'=>255, 'data-language'=>lang())); ?>
				<?=$form->error($model,'meta_keywords'); ?>
			</span>
		</div>	
   	</div>
<?php $this->endWidget(); ?>

<?php endif ?>