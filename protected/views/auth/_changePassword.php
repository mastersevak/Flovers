<?$modal = $this->beginWidget('UIModal',[
	'id' 	=> 'change-password-modal',
	'width' => 400,
	'title' => t('back', 'Смена пароля'),
	'bodyClass' => 'body'
	]);

$form = $this->beginWidget('SActiveForm', [
	'id'=>'change-password-form',
	'modal' => true,
	'enableAjaxValidation' => true,
	'action' => ['/auth/changepassword', 'id'=>$model->id],
	'afterModalClose' => 'function(form, data){
		form.get(0).reset();
		Forms.enableFormSubmit(form);

	}'
]);
$modal->header();?>

<div class="mt20 mb0 grid simple">

	<div class="grid-body no-border clearfix">

		<div class="contacts-form">
			<div class="wrap-paper" style="height: 110px">
				<div class="paper">

					<div class="form-group">
						<span class="icon icon-user"></span>
						<?=$form->textField($model, 'old_password',['class'=>"form-control", 'placeholder'=>t('back', 'Старый пароль')])?>
						<?=$form->error($model,'old_password')?>
					</div>

					<div class="form-group">
						<span class="icon icon-user"></span>
						<?=$form->textField($model, 'password',['class'=>"form-control", 'placeholder'=>t('back', 'Пароль')])?>
						<?=$form->error($model,'password')?>
					</div>

					<div class="form-group">
						<span class="icon icon-user"></span>
						<?=$form->textField($model, 'password2',['class'=>"form-control", 'placeholder'=>t('back', 'Повторите пароль')])?>
						<?=$form->error($model,'password2')?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?$modal->footer();

$this->endWidget(); // form
$this->endWidget(); // modal ?>