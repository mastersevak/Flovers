{form name='form'
	id='edit-form'
	enableAjaxValidation=true}


<p class="note mb20">Поля со знаком <code>*</code> объязательны для заполнения</p>

<div class="std-form">

	<div class="control-group">
		{$form->labelEx($model,'title')}
		<span class="field">
			{$form->multilangTextField($model,'title', [
					'data-slug-to'=>'Block_slug',
					'data-slugger'=>$this->createUrl('ajaxslug')] )}
			{$form->error($model,'title')}
		</span>
	</div>

	<div class="control-group">
		{$form->labelEx($model, 'slug')}
		<span class="field">
			{$form->slugField($model, 'slug')}
			{$form->error($model, 'slug')}
		</span>
	</div>

	<div class="control-group <?=lang()?> elrte_editor">
		{$form->labelEx($model, 'content')}
		<span class="field">
			{$form->multilangElrteEditor($model, 'content')}
			{$form->error($model, 'content')}
		</span>
	</div>

	<div class="buttons mt20">
	{if $model->isNewRecord}
		{widget name='UIButtons' group='create' id=$model->id}
	{else}
		{widget name='UIButtons' group='update' id=$model->id}
	{/if}
	</div>

</div>

{/form}