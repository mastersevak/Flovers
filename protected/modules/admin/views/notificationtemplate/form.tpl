<div class="grid simple mt20">
	<div class="grid-body">

		{form name='form'
			id='notification-template-form'
			enableAjaxValidation=true}


			<p class="note mb20">Поля со знаком <code>*</code> объязательны для заполнения</p>

		    <div class="std-form">

		    	<div class="control-group">
		        	{$form->labelEx($model, 'type')}
		            <span class="field">
		            	{$form->dropDownList($model, 'type', NotificationTemplate::$types, ['class'=>'uniform'] )}
		            	{$form->error($model,'status')}
		            </span>
		        </div>

		        <div class="control-group">
					{$form->labelEx($model, 'slug')}
					<span class="field">
						{$form->textField($model, 'slug', ['maxlength'=>255])}
						{$form->error($model,'slug')}
					</span>
		        </div>

		        <div class="control-group">
					{$form->labelEx($model, 'title')}
					<span class="field">
						{$form->textField($model, 'title', ['maxlength'=>255])}
						{$form->error($model,'title')}
					</span>
		        </div>

		        <div class="control-group">
					{$form->labelEx($model, 'subject')}
					<span class="field">
						{$form->textField($model, 'subject', ['maxlength'=>255])}
						{$form->error($model,'subject')}
					</span>
		        </div>

		        <div class="control-group elrte_editor">
		        	{$form->labelEx($model, 'body')}
					<span class="field">
						{$form->elrteEditor($model, 'body')}
						{$form->error($model, 'body')}
					</span>
		        </div>

		        <div class="buttons mt20">
				{if $model->isNewRecord}
					{widget name='UIButtons' group='create' id=$model->id}
				{else}
					{widget name='UIButtons' group='update' id=$model->id}
				{/if}	
				</div>

		    </div><!--stdform stdform2-->

		{/form}

	</div>
</div>