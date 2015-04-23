<?php
$model->setScenario('flogin');
$modal = $this->beginWidget('UIModal',[
		'id' => 'login-modal',
		'width' => 400,
		'title' => t('front', 'Вход'),
		'draggable' => false,
	]);
	?>

<div class="bg-lighten-gray p20 clearfix">
	<?php $form = $this->beginWidget('SActiveForm', array(
			'id'=>'login-form-modal',
			'modal' => true,
			'action' => ['/auth/ajaxlogin'],
			'focus' => [$model, 'username'],
			'enableAjaxValidation' => true,
			'afterModalClose' => 'function(form, data){
				location.reload();
			}'
		));
		echo CHtml::hiddenField('scenario', $model->scenario);?>

		<div class="contacts-form">
			<div class="wrap-paper" style="height: 110px">
				<div class="paper">
					<div class="form-group">
						<span class="icon icon-user"></span>
						<?=$form->textField($model, 'username',['class'=>"form-control", 'placeholder'=>t('back', 'Имя пользователя')])?>
					</div>
						<?=$form->error($model,'username')?>

					<div class="form-group">
						<span class="icon icon-user"></span>
						<?=$form->passwordField($model, 'password',['class'=>"form-control", 'placeholder'=>t('back', 'Пароль')])?>
					</div>
						<?=$form->error($model,'password')?>
				</div>
			</div>
		</div>

		<div class="form-link" style="  float: left; margin-left: 60px;"><a href="#"><?=t('back', 'Забыли пароль?')?></a></div>

		<?$this->widget('UIButtons', ['buttons' => [
			'custom' => [
				'value'		=> t('back', 'Войти'),
				'icon'		=> '',
				'options'	=> [
					'class'			=> 'btn btn-mega',
					'style'			=>	'float: right;   margin-bottom: 20px; margin-right: 60px;',
					'data-form' 	=> 'login-form',
					'type'			=> 'submit',
					'onclick'		=> 'UIButtons.save(this); return false;'
				]
			]]
		]);?>
	<?php $this->endWidget(); // form ?>
</div>
<?$this->endWidget(); // modal ?>
