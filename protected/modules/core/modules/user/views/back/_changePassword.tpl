{begin_widget name='UIModal' 
			id='change-password-modal'
	        width=400
	        bodyClass='body'
	        title='Смена пароля'}

{$modal = $widget}{*это делается потому что на следующем шаге $widget = form*}

{form name="form" 
	modal=true 
	enableAjaxValidation=true
	action=['back/changepassword', 'id'=>$model->id]}

{$modal->header()}

<div class="mt20 mb0 grid simple">
		
	<div class="grid-body no-border clearfix">

		<div class="control-group">
			{$form->labelEx($model, 'old_password')}
			{$form->passwordField($model, 'old_password')}
			{$form->error($model, 'old_password')}
		</div>

		<div class="control-group">
			{$form->labelEx($model, 'password')}
			{$form->passwordField($model, 'password')}
			{$form->error($model, 'password')}
		</div>

		<div class="control-group">
			{$form->labelEx($model, 'password2')}
			{$form->passwordField($model, 'password2')}
			{$form->error($model, 'password2')}
		</div>

	</div>

</div>

{$modal->footer()}

{/form}

{/begin_widget}