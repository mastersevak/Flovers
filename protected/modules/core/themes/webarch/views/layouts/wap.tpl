{$this->beginContent('//layouts/prepare')}
	{*HEADER*}
	{$this->renderPartial('//layouts/parts/_headerWap')}
		
		{CHtml::hiddenField('id_user', Yii::app()->user->id, ['data-url' => $this->createUrl('saveCourierLocation')])}
		
		<div class="wap page-content">
			<div class="content {if !$this->pageTitle}mt70{/if}">
				{$content}
			</div>

			{if $this->action->id != 'index'}
			<footer class="clearfix">
				{CHtml::link('<i class="fa fa-arrow-circle-left"></i>', ['index'], ['class' => 'btn btn-info fl'])}
				{CHtml::link('<i class="fa fa-refresh"></i>', '#', ['class' => 'btn btn-warning fr', 'onclick' => "location.reload()"])}
				{$this->statusButton}
			</footer>
			{/if}
		</div>

{$this->endContent()}