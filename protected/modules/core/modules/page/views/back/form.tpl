{begin_widget name='UITabs' tabs=$this->tabs}

{form name='form'
	id='edit-form'
	enableAjaxValidation=true}

<div class="tab-content">
	<div id="main" class="tab-pane active">

		<p class="note mb20">Поля со знаком <code>*</code> объязательны для заполнения</p>

		<div class="std-form">

			<div class="control-group">
				{$form->labelEx($model, 'status')}
				<span class="field">
					{$form->dropDownList($model, 'status', Lookup::items('StandartStatus'))}
					{$form->error($model, 'status')}
				</span>
			</div>

			<div class="control-group">
				{$form->labelEx($model,'title')}
				<span class="field">
					{$form->multilangTextField($model,'title', [
							'data-slug-to'=>'Page_slug',
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

			<div class="control-group">
				{$form->labelEx($model, 'route')}
				<span class="field">
					{$form->textField($model, 'route')}
					{$form->error($model, 'route')}
				</span>
			</div>

			<div class="control-group elrte_editor">
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

	</div>

	<div id="meta" class="tab-pane">

		<div class="std-form">

			<div class="control-group">
				{$form->labelEx($model, 'meta_title')}
				<span class="field">
					{$form->multilangTextField($model, 'meta_title', ['data-limit'=>65])}
					{$form->error($model, 'meta_title')}
				</span>
			</div>

			<div class="control-group">
				{$form->labelEx($model, 'meta_description')}
				<span class="field">
					{$form->multilangTextField($model, 'meta_description', ['data-limit'=>170])}
					{$form->error($model,'meta_description')}
				</span>
			</div>

			<div class="control-group">
				{$form->labelEx($model, 'meta_keywords')}
				<span class="field">
					{$form->multilangTextArea($model, 'meta_keywords')}
					{$form->error($model, 'meta_keywords')}
				</span>
			</div>
		</div>
	</div>
</div>

{/form}

{/begin_widget}