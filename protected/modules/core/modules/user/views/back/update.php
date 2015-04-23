<?php
$this->renderPartial('_changePassword', ['model'=> $changePasswordModel]);
?>

<? $this->beginWidget('UITabs', [
    'paramName' => 'type',
    'tabs' => $this->tabs,
]); ?>

<? $form = $this->beginWidget('SActiveForm', [
	'id'	=>'edit-form', //объязательно для работы кнопок сохранения, удаления
	'htmlOptions'=>[
		'enctype'=>'multipart/form-data'
	]
]); ?>

<div class="tab-content">
	<div id="main" class="tab-pane active">
		<p class="note mb20">Поля со знаком <code>*</code> объязательны для заполнения</p>

		<div class="row">
			<div class="std-form col-md-6">
				<div class="control-group">
					<?=$form->labelEx($model, 'status')?>
					<span class="field">
						<?=$form->dropDownList($model, 'status', Lookup::items('StandartStatus'),
							['class'=>'uniform', 'data-width' => '260px'])?>
						<?=$form->error($model,'status')?>
					</span>
				</div>

				<div class="control-group">
					<?=$form->labelEx($model, 'username')?>
					<span class="field">
						<?=$form->textField($model, 'username', ['readOnly' => true])?>
						<?=$form->error($model,'username')?>
					</span>
				</div>

				<div class="control-group">
					<?=$form->labelEx($model, 'email')?>
					<span class="field">
						<?=$form->textField($model, 'email')?>
						<?=$form->error($model,'email')?>
					</span>
				</div>

				<div class="control-group bg-blue">
					<?=$form->labelEx($model, 'image')?>
					<span class="field">
						<?$this->widget('Avatar', [
							'form' 			=> $form,
							'model' 		=> $model,
							'field' 		=> 'image',
							'image' 		=> 'avatar',
							'size'			=> 'big',
							'hiddenFile' 	=> true,
							'thumbWidth' 	=> param('images/user/sizes/big/width'),
							'thumbHeight' 	=> param('images/user/sizes/big/height'),
							'bigSize'		=> 'big',
							'alt' 			=> $model->fullname,
						]);?>
					</span>
				</div>

				<? if(!$model->isSocial() && Yii::app()->user->isRole('admin')) : ?>
				<div class="control-group">
					<?=$form->labelEx($model, 'password')?>
					<span class="field">
						<?=CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-key'], '').t('user', "Сменить пароль"), [
							'class'=>'btn btn-warning',
							'data-toggle'=>"domodal",
							'data-target'=>"#change-password-modal"
						]);?>
					</span>
				</div>
				<? endif ?>

			</div>
		
			<div class="std-form col-md-6">
				<div class="control-group">
					<?=$form->labelEx($model, 'firstname')?>
					<span class="field">
						<?=$form->textField($model, 'firstname')?>
						<?=$form->error($model,'firstname')?>
					</span>
				</div>

				<div class="control-group">
					<?=$form->labelEx($model, 'lastname')?>
					<span class="field">
						<?=$form->textField($model, 'lastname')?>
						<?=$form->error($model,'lastname')?>
					</span>
				</div>

				<div class="control-group">
					<?=$form->labelEx($model, 'middlename')?>
					<span class="field">
						<?=$form->textField($model, 'middlename')?>
						<?=$form->error($model,'middlename')?>
					</span>
				</div>

			</div>
		</div>

	</div>
		
	<div id="more" class="tab-pane std-form row">
		<div class="col-md-6">
			<div class="control-group">
				<?=$form->labelEx($model, 'created')?>
				<span class="field">
					<? $createdText = $model->created ? Yii::app()->dateFormatter->format("dd MMM y, HH:mm:ss ", $model->timestamp('created')) : ''; ?>
					<? $createdText .= $model->id_creator ? ' ['.User::getUserFromCache($model->id_creator).']' : ''; ?>

					<?=CHtml::textField('id_creator', $createdText, ['readOnly' => true])?>
				</span>
			</div>

			<div class="control-group">
				<?=$form->labelEx($model, 'changed')?>
				<span class="field">	
					<? $changedText = $model->changed ? Yii::app()->dateFormatter->format("dd MMM y, HH:mm:ss ", $model->timestamp('changed')) : '' ?>
					<? $changedText .= $model->id_changer ? ' ['.User::getUserFromCache($model->id_changer).']' : '' ?>

					<?=CHtml::textField('id_changer', $changedText, ['readOnly' => true])?>
				</span>
			</div>

			<div class="control-group">
				<?=$form->labelEx($model, 'activated')?>
				<span class="field">
					<?=$form->dateField($model, 'activated')?>
				</span>
			</div>

			<div class="control-group">
				<?=$form->labelEx($model, 'last_visit')?>
				<span class="field">
					<?=$form->dateField($model, 'last_visit')?>
				</span>
			</div>

			<div class="control-group">
				<?=$form->labelEx($model, 'registration_ip')?>
				<span class="field">
					<?=$form->disableField($model, 'registration_ip')?>
				</span>
			</div>

			<div class="control-group">
				<?=$form->labelEx($model, 'activation_ip')?>
				<span class="field">
					<?=$form->disableField($model, 'activation_ip')?>
				</span>
			</div>
		</div>
	</div> 

	<?if($model->is_social_user && Yii::app()->user->isRole('admin')) : ?>
	<div id="auth" class="tab-pane std-form">
		<? if($model->service_user_pic) : ?>
		<div class="control-group">
			<?=$form->labelEx($model, 'service_user_pic')?>
			<?=CHtml::image($model->service_user_pic)?>
		</div>
		<? endif ?>

		<div class="control-group">
			<?=$form->labelEx($model, 'service_name')?>
			<?=$form->disableField($model->service_name)?>
		</div>

		<div class="control-group">
			<?=$form->labelEx($model, 'service_user_id')?>
			<?=$form->disableField($model->service_user_id)?>
		</div>

		<div class="control-group">
			<?=$form->labelEx($model, 'service_user_name')?>
			<?=$form->disableField($model->service_user_name)?>
		</div>

		<div class="control-group">
			<?=$form->labelEx($model, 'service_user_email')?>
			<?=$form->disableField($model->service_user_email)?>
		</div>

		<? if($model->service_user_url) : ?>
		<div class="control-group">
			<?=$form->labelEx($model, 'service_user_url')?>
			<?=CHtml::link($model->service_user_url, $model->service_user_url, ['target'=>'_blank'])?>
		</div>
		<? endif ?>
	</div>
	<? endif ?>	
</div>

<? $this->endWidget(); ?>
<? $this->endWidget(); ?>

<div class='fr'>
	<?$this->widget('UIButtons', ['group'=>'save', 'form'=>'edit-form', 'id'=>$model->id])?>
</div>
