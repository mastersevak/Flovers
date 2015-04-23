
<?php $form=$this->beginWidget('SActiveForm', array(
	'id'=>'edit-form', //объязательно для работы кнопок сохранения, удаления
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data'
	)
)); 

// echo $form->errorSummary($model);
?>

<!--main info-->  
<div id="main" class="subcontent">

	<? $this->renderPartial('//main/update-buttons', array('id'=>$model->id)); ?>

    <p class="note"><?=t('admin','Fields with * are required.')?></p>

    <div class="stdform stdform2">
    	
    	<div class="par">
        	<?=$form->labelEx($model, 'status'); ?>
            <span class="field">
            	<?=$form->dropDownList($model, 'status', 
            			Lookup::items('StandartStatus'), array('class'=>'uniform') ); ?>
            	<?=$form->error($model,'status'); ?>
            </span>
        </div>

        <div class="par">
			<?=$form->labelEx($model,'date'); ?>
			<span class="field">
				<? Common::datePicker($model, 'date'); ?>
				<?=$form->error($model,'date'); ?>
			</span>
        </div>


        <div class="par">
			<?=$form->labelEx($model, 'title'); ?>
			<span class="field">
				<?=$form->textField($model, 'title', 
						array(
							'maxlength'=>255, 
							'data-language'=>lang(),
							'data-slug-to'=>get_class($model).'_slug',
							'data-slugger'=>$this->createUrl('ajaxslug')) ); ?>
				<?=$form->error($model,'title'); ?>
			</span>
        </div>

        <div class="par">
			<?=$form->labelEx($model,'slug'); ?>
			<span class="slug field clearfix">
				<span class="slug-checkbox">
					<?=CHtml::checkBox('slug_set')?>
					<?=CHtml::label('В ручную', 'slug_set')?>
				</span>

				<span class="slug-text">
					<?=$form->textField($model, 'slug', array(
							'maxlength'=>255, 
							'readonly'=>true)); ?>
					<?=$form->error($model,'slug'); ?>
				</span>
			</span>
        </div>

        <div class="par">
			<?=$form->labelEx($model, 'image'); ?>
			<span class="field">
				<? $this->widget('Avatar', array(
					'form'		   => $form,
					'model'        => $model,
					'field'        => 'image',
					'size' 		   => 'thumb',
					'thumbWidth'   => param('images/photoalbumThumb/sizes/thumb/width'),
					'thumbHeight'  => param('images/photoalbumThumb/sizes/thumb/height'),
					'bigSize' 	   => 'thumb',
				)) ?>
			</span>
        </div>

    </div><!--stdform stdform2-->


    <? $this->renderPartial('//main/update-buttons', array('id'=>$model->id)); ?>
		
</div><!--subcontent-->

<div id="photos" class="subcontent">
	<? $this->widget('Uploader', compact('files', 'model', 'params')) ?>
</div>

<?php $this->endWidget(); ?>
