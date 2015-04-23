{$this->beginContent('//layouts/prepare')}
	{*HEADER*}
	{$this->renderPartial('//layouts/parts/_header')}
	
	<div class="page-content tabs {if UIHelpers::hideSideBar()}condensed{/if}">

		<div class="page-header clearfix">
			{$this->renderPartial('//layouts/parts/_breadcrumbs')}
			<div class="page-title">
				<h3>{Yii::app()->format->custom('normal<semibold>', $this->pageTitle)}</h3>
				
				{if $this->pageDesc}
				 	<p class="mb20">{$this->pageDesc}</p>
				{/if}
			</div>

			{$this->filters}
		</div>

		<div class="content">
		
			{$content}

		</div>
	</div>

{$this->endContent()}

<a href="#" class="scrollup"></a>
