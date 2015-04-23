{begin_widget name='UIModal' 
			id='register-modal'
	        width=800
	        bodyClass='body'
	        title='Регистрация пользователя'}
	        
{$modal = $widget}{*это делается потому что на следующем шаге $widget = form*}

{form name='form'
	modal=true 
	action=['/core/user/back/registration']
	enableAjaxValidation=true
	afterModalClose= 'function(form){
			$.fn.yiiGridView.update("users-table"); //update table
		}'
}

{$modal->header()}

<div class="mt20">
	<div class="grid simple">
		
		<div class="grid-body no-border clearfix">
				
			<p class="mb20">{t('admin', 'required_fields')}</p>

			<div class="row">
				<div class="col-md-6">

					<div class="control-group">
						{$form->labelEx($model, 'email')}
						{$form->textField($model, 'email')}
						{$form->error($model, 'email')}
					</div>

					<div class="control-group">
						{$form->labelEx($model, 'username')}
						{$form->textField($model, 'username')}
						{$form->error($model, 'username')}
					</div>

					<div class="row">
						<div class="control-group col-md-6">
							{$form->labelEx($model, 'password')}
							{$form->passwordField($model, 'password')}
							{$form->error($model, 'password')}
						</div>

						<div class="control-group col-md-6">
							{$form->labelEx($model, 'password2')}
							{$form->passwordField($model, 'password2')}
							{$form->error($model, 'password2')}
						</div>
					</div>
					
				</div>
				
				<div class="col-md-6">
					<div class="control-group">
						{$form->labelEx($model,'firstname')}
						{$form->textField($model,'firstname')}
						{$form->error($model,'firstname')}
					</div>

					<div class="control-group">
						{$form->labelEx($model,'lastname')}
						{$form->textField($model,'lastname')}
						{$form->error($model,'lastname')}
					</div>

					<div class="control-group">
						{$form->labelEx($model,'middlename')}
						{$form->textField($model,'middlename')}
						{$form->error($model,'middlename')}
					</div>
				</div>

			</div>
				
		</div> <!-- // grid body -->
	</div>
</div>

{$modal->footer()}

{/form}

{/begin_widget}