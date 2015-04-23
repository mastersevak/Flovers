{begin_widget name='UITabs' tabs=$this->tabs}

{form name='form'
	id='edit-form'
	enableAjaxValidation=true
	htmlOptions = [
		enctype => 'multipart/form-data'
	]
}

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
					{$form->textField($model,'title', [
							'data-slug-to'=>'News_slug',
							'data-language'=>lang(),
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
				{$form->labelEx($model, 'short_content')}
				<span class="field">
					{$form->textField($model, 'short_content',
							['data-limit'=>70, 'data-language'=>lang()])}
					{$form->error($model, 'short_content')}
				</span>
			</div>

			<div class="control-group bg-blue">
				{$form->labelEx($model, 'image')}
				<span class="field">
					{widget name 		= 'Avatar'
							form 		= $form
							model 		= $model
							field 		= 'image'
							image 		= 'thumbnail'
							size		= 'big'
							hiddenFile 	= true
							thumbWidth 	= param('images/news/sizes/big/width')
							thumbHeight = param('images/news/sizes/big/height')
							bigSize		= 'big'
							alt 		= $model->title}
				</span>
			</div>

			<div class="control-group {lang()} elrte_editor">
				{$form->labelEx($model, 'content')}
				<span class="field">
					{$form->elrteEditor($model, 'content')}
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
					{$form->textField($model, 'meta_title',
							['data-limit'=>65, 'data-language'=>lang()])}
					{$form->error($model, 'meta_title')}
				</span>
			</div>

			<div class="control-group">
				{$form->labelEx($model, 'meta_description')}
				<span class="field">
					{$form->textArea($model, 'meta_description',
							['data-limit'=>170, 'data-language'=>lang()])}
					{$form->error($model,'meta_description')}
				</span> 
			</div>

			<div class="control-group">
				{$form->labelEx($model, 'meta_keywords')}
				<span class="field">
					{$form->textArea($model, 'meta_keywords', ['data-language'=>lang()])}
					{$form->error($model, 'meta_keywords')}
				</span>
			</div>
			
		</div>

	</div>

</div>

{/form}

{/begin_widget}